<?php
namespace Kz370\ScollioLogger\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Kz370\ScollioLogger\Models\LogEntry;

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
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }
        if ($request->filled('q')) {
            $query->where('message', 'like', '%' . $request->q . '%');
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(
            config('scollio-logger.dashboard.pagination', 15)
        );

        $levels   = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        $channels = LogEntry::select('channel')->distinct()->pluck('channel');

        $theme  = config('scollio-logger.dashboard.theme', 'auto');
        $colors = config('scollio-logger.colors', [
            'emergency' => 'bg-red-900 text-white',
            'alert'     => 'bg-red-700 text-white',
            'critical'  => 'bg-red-600 text-white',
            'error'     => 'bg-red-500 text-white',
            'warning'   => 'bg-yellow-400 text-black',
            'notice'    => 'bg-blue-200 text-black',
            'info'      => 'bg-blue-500 text-white',
            'debug'     => 'bg-gray-200 text-black',
        ]);
        return view('scollio-logger::dashboard', ['showSingle' => true, 'logs' => $logs, 'levels' => $levels, 'channels' => $channels, 'theme' => $theme, 'colors' => $colors]);
    }

    public function show($id)
    {
        $log      = LogEntry::findOrFail($id);
        $levels   = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        $channels = LogEntry::select('channel')->distinct()->pluck('channel');

        $theme  = config('scollio-logger.dashboard.theme', 'auto');
        $colors = config('scollio-logger.colors', [
            'emergency' => 'bg-red-900 text-white',
            'alert'     => 'bg-red-700 text-white',
            'critical'  => 'bg-red-600 text-white',
            'error'     => 'bg-red-500 text-white',
            'warning'   => 'bg-yellow-400 text-black',
            'notice'    => 'bg-blue-200 text-black',
            'info'      => 'bg-blue-500 text-white',
            'debug'     => 'bg-gray-200 text-black',
        ]);

        return view('scollio-logger::dashboard', [
            'showSingle' => true,
            'log'        => $log,
            'levels'     => $levels,
            'channels'   => $channels,
            'theme'      => $theme,
            'colors'     => $colors,
            'logs'       => collect(), // empty list when showing single
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

        return redirect()->route('scollio-logs.index')->with('status', "$count log(s) cleared");
    }
}
