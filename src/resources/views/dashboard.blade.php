<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Scollio Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900">
<div class="container mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-bold">Scollio Logs</h1>
        <form method="POST" action="{{ route('scollio-logs.clear') }}">
            @csrf
            <button class="px-3 py-1 bg-red-600 text-white rounded">Clear Logs</button>
        </form>
    </div>

    <form method="GET" class="mb-4">
        <div class="grid grid-cols-6 gap-2">
            <select name="level" class="col-span-1 p-2 border rounded">
                <option value="">All levels</option>
                @foreach($levels as $lvl)
                    <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                @endforeach
            </select>
            <select name="channel" class="col-span-1 p-2 border rounded">
                <option value="">All channels</option>
                @foreach($channels as $ch)
                    <option value="{{ $ch }}" {{ request('channel') == $ch ? 'selected' : '' }}>{{ $ch }}</option>
                @endforeach
            </select>
            <input type="text" name="location" placeholder="Location" value="{{ request('location') }}" class="col-span-2 p-2 border rounded">
            <input type="date" name="date" value="{{ request('date') }}" class="p-2 border rounded">
            <input type="text" name="q" placeholder="Search" value="{{ request('q') }}" class="p-2 border rounded col-span-1">
            <div class="col-span-6 mt-2">
                <button class="px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
                <a href="{{ route('scollio-logs.index') }}" class="ml-2 px-3 py-1 bg-gray-200 rounded">Reset</a>
            </div>
        </div>
    </form>

    @if(session('status'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    @if(isset($showSingle) && $showSingle === true && isset($log))
        <div class="bg-white rounded shadow p-4">
            <h2 class="text-xl font-semibold mb-2">Log #{{ $log->id }}</h2>
            <div class="mb-2"><strong>Level:</strong> <span class="px-2 py-1 rounded {{ $log->color }}">{{ $log->level }}</span></div>
            <div class="mb-2"><strong>Message:</strong> {{ $log->message }}</div>
            <div class="mb-2"><strong>Context:</strong><pre>{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre></div>
            <a href="{{ route('scollio-logs.index') }}" class="inline-block mt-2 px-3 py-1 bg-gray-200 rounded">Back</a>
        </div>
    @else
        <div class="bg-white rounded shadow overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">#</th>
                        <th class="px-4 py-2 text-left">Level</th>
                        <th class="px-4 py-2 text-left">Message</th>
                        <th class="px-4 py-2 text-left">Location</th>
                        <th class="px-4 py-2 text-left">Channel</th>
                        <th class="px-4 py-2 text-left">When</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $log->id }}</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 rounded {{ $log->color }}">{{ $log->level }}</span></td>
                            <td class="px-4 py-2">{{ Str::limit($log->message, 80) }}</td>
                            <td class="px-4 py-2">{{ $log->location }}</td>
                            <td class="px-4 py-2">{{ $log->channel }}</td>
                            <td class="px-4 py-2">{{ $log->created_at }}</td>
                            <td class="px-4 py-2">
                                <a href="{{ route('scollio-logs.show', $log->id) }}" class="px-2 py-1 bg-blue-500 text-white rounded">Show</a>
                                <form method="POST" action="{{ route('scollio-logs.delete', $log->id) }}" style="display:inline-block">
                                    @method('DELETE')
                                    @csrf
                                    <button class="px-2 py-1 bg-red-500 text-white rounded">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    @endif
</div>
</body>
</html>
