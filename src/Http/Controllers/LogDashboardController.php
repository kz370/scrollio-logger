<?php

namespace Scollio\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Scollio\Models\LogEntry;
use Illuminate\Support\Facades\DB;

class LogDashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = LogEntry::query();

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Combine date and time filters to datetime range
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $fromTime = $request->input('from_time', '00:00');
        $toTime = $request->input('to_time', '23:59');

        if ($fromDate || $toDate) {
            $from = $fromDate ? $fromDate . ' ' . $fromTime : null;
            $to = $toDate ? $toDate . ' ' . $toTime : null;
            if ($from && $to) {
                $query->whereBetween('created_at', [$from, $to]);
            } elseif ($from) {
                $query->where('created_at', '>=', $from);
            } elseif ($to) {
                $query->where('created_at', '<=', $to);
            }
        }

        if ($request->filled('q')) {
            $query->where('message', 'like', '%' . $request->q . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(
            config('scollio-logger.dashboard.pagination', 15)
        )->appends($request->except('page'));

        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        $channels = LogEntry::select('channel')->distinct()->pluck('channel');

        $theme = config('scollio-logger.dashboard.theme', 'auto');
        $colors = config('scollio-logger.colors', [
            'emergency' => 'bg-red-900 text-white',
            'alert' => 'bg-red-700 text-white',
            'critical' => 'bg-red-600 text-white',
            'error' => 'bg-red-500 text-white',
            'warning' => 'bg-yellow-400 text-black',
            'notice' => 'bg-blue-200 text-black',
            'info' => 'bg-blue-500 text-white',
            'debug' => 'bg-gray-200 text-black',
        ]);

        // Handle AJAX requests
        if ($request->ajax()) {
            $tableHtml = view('scollio-logger::partials.logs-table', [
                'logs' => $logs,
                'colors' => $colors
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $tableHtml,
                'pagination' => $logs->hasPages() ? $logs->links()->render() : '',
                'total' => $logs->total(),
                'showing' => [
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                    'total' => $logs->total()
                ]
            ]);
        }

        return view('scollio-logger::dashboard', [
            'showSingle' => false,
            'logs' => $logs,
            'levels' => $levels,
            'channels' => $channels,
            'theme' => $theme,
            'colors' => $colors,
        ]);
    }

    public function show($id)
    {
        $log = LogEntry::findOrFail($id);
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        $channels = LogEntry::select('channel')->distinct()->pluck('channel');

        $theme = config('scollio-logger.dashboard.theme', 'auto');
        $colors = config('scollio-logger.colors', [
            'emergency' => 'bg-red-900 text-white',
            'alert' => 'bg-red-700 text-white',
            'critical' => 'bg-red-600 text-white',
            'error' => 'bg-red-500 text-white',
            'warning' => 'bg-yellow-400 text-black',
            'notice' => 'bg-blue-200 text-black',
            'info' => 'bg-blue-500 text-white',
            'debug' => 'bg-gray-200 text-black',
        ]);

        return view('scollio-logger::dashboard', [
            'showSingle' => true,
            'log' => $log,
            'levels' => $levels,
            'channels' => $channels,
            'theme' => $theme,
            'colors' => $colors,
            'logs' => collect(),
        ]);
    }

    public function delete($id)
    {
        LogEntry::where('id', $id)->delete();
        return redirect()->route('scollio-logs.index')->with('status', 'Log deleted');
    }

    public function clear(Request $request)
    {
        $query = LogEntry::query();

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        $count = $query->delete();

        if (!$request->filled('level') && !$request->filled('channel')) {
            $tableName = config('scollio-logger.table', 'scollio_logs');
            DB::statement("ALTER TABLE {$tableName} AUTO_INCREMENT = 1");
        }

        return redirect()->route('scollio-logs.index')->with('status', "$count log(s) cleared");
    }
}
