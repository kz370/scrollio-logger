<?php

namespace Kz370\ScollioLogger\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Kz370\ScollioLogger\Models\ScollioLogger;

class ScollioLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ScollioLogger::query()->orderByDesc('id');

        if ($q = $request->get('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('url', 'like', "%{$q}%")
                    ->orWhere('exception_message', 'like', "%{$q}%");
            });
        }

        if ($code = $request->get('status_code')) {
            $query->where('status_code', (int) $code);
        }

        if ($from = $request->get('from_date')) {
            $query->whereDate('requested_at', '>=', $from);
        }
        if ($to = $request->get('to_date')) {
            $query->whereDate('requested_at', '<=', $to);
        }

        $paginateValue = config('scollio-logger.paginate', 15);
        $logs = $query->paginate($paginateValue)->appends($request->query());

        if ($request->ajax()) {
            $html = View::make('scollio::partials.logs-table', compact('logs'))->render();
            $pagination = $logs->hasPages() ? $logs->links()->render() : '';
            return response()->json([
                'success' => true,
                'html' => $html,
                'pagination' => $pagination,
            ]);
        }

        $colors = [];
        return view('scollio::index', compact('logs', 'colors'));
    }


    public function poll(Request $request)
    {
        $lastId = (int) $request->query('last_id', 0);
        $table = (new ScollioLogger())->getTable();

        // Wait up to 30 seconds for a new log
        $timeout = 30;
        $start = time();
        do {
            $new = ScollioLogger::where('id', '>', $lastId)->orderBy('id', 'desc')->first();
            if ($new) {
                $logs = ScollioLogger::orderBy('id', 'desc')->take(50)->get();
                $html = view('scollio::partials.logs-table', [
                    'logs' => $logs,
                    'colors' => config('scollio-logger.colors', [])
                ])->render();
                return response()->json([
                    'success' => true,
                    'html' => $html,
                    'last_id' => $new->id
                ]);
            }
            usleep(1000000); // 0.5s sleep
        } while (time() - $start < $timeout);

        return response()->json(['success' => false]);
    }


    public function show($id)
    {
        $log = ScollioLogger::findOrFail($id);

        // Decode and format the request body
        $requestBody = $log->body;
        if (is_array($requestBody)) {
            $requestBody = json_encode($requestBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } elseif (is_string($requestBody)) {
            $decoded = json_decode($requestBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $requestBody = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        // Decode and format the response body
        $responseBody = $log->response_body;
        if (is_string($responseBody)) {
            $decodedResponse = json_decode($responseBody, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $responseBody = json_encode($decodedResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } else {
                // fallback: truncate large strings but preserve text
                $responseBody = Str::limit($responseBody, 50000, '... [truncated]');
            }
        }

        return view('scollio::show', [
            'log'           => $log,
            'colors'        => [],
            'requestBody'   => $requestBody,
            'responseBody'  => $responseBody,
        ]);
    }


    public function destroy($id, Request $request)
    {
        $log = ScollioLogger::findOrFail($id);
        $log->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('scollio-logs.index')->with('status', 'Log deleted.');
    }

    public function clear(Request $request)
    {
        // Delete all records
        ScollioLogger::query()->delete();

        // Get the actual table name from the model
        $table = (new ScollioLogger())->getTable();

        // Reset auto-increment / sequence based on database driver
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER SEQUENCE {$table}_id_seq RESTART WITH 1");
        }

        // Return response
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('scollio-logs.index')
            ->with('status', 'All logs cleared and index reset.');
    }
}
