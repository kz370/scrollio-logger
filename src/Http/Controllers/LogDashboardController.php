<?php

namespace Kz370\ScollioLogger\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
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
            config('scollio-logger.pagination', 15)
        );

        $levels = ['emergency','alert','critical','error','warning','notice','info','debug'];
        $channels = LogEntry::select('channel')->distinct()->pluck('channel');

        return view('scollio-logger::dashboard', compact('logs', 'levels', 'channels'));
    }

    public function show($id)
    {
        $log = LogEntry::findOrFail($id);
        $levels = ['emergency','alert','critical','error','warning','notice','info','debug'];
        $channels = LogEntry::select('channel')->distinct()->pluck('channel');

        return view('scollio-logger::dashboard', [
            'showSingle' => true,
            'log' => $log,
            'levels' => $levels,
            'channels' => $channels,
            'logs' => collect(), // empty list when showing single
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
