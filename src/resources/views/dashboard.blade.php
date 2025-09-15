<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scollio Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Optimized solid backgrounds */
        .solid-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .dark .solid-card {
            background: #1f2937;
            border: 1px solid #374151;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Simple gradient without animation */
        .simple-bg {
            background: #f8fafc;
        }

        .dark .simple-bg {
            background: #111827;
        }

        /* Minimal hover effects */
        .btn-hover {
            transition: transform 0.15s ease;
            will-change: transform;
        }

        .btn-hover:hover {
            transform: translateY(-1px);
        }

        /* Optimized scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        .dark ::-webkit-scrollbar-track {
            background: #374151;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #6b7280;
        }

        /* Fixed form styling with better dark mode support */
        .form-input {
            color: #374151 !important;
            background-color: #ffffff !important;
            border: 1px solid #d1d5db;
            transition: border-color 0.15s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .dark .form-input {
            color: #f9fafb !important;
            background-color: #374151 !important;
            border: 1px solid #4b5563;
        }

        .dark .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* Fixed text colors for better readability */
        .text-primary {
            color: #111827;
        }

        .dark .text-primary {
            color: #f9fafb;
        }

        .text-secondary {
            color: #6b7280;
        }

        .dark .text-secondary {
            color: #9ca3af;
        }

        .text-blue {
            color: #2563eb;
        }

        .dark .text-blue {
            color: #60a5fa;
        }

        /* Optimized table row hover */
        .table-row {
            transition: background-color 0.1s ease;
        }

        .table-row:hover {
            background-color: #f9fafb;
        }

        .dark .table-row:hover {
            background-color: #374151;
        }

        /* Hidden class for filtering */
        .filtered-hidden {
            display: none !important;
        }

        /* Better header styling */
        .header-text {
            color: #1f2937;
        }

        .dark .header-text {
            color: #f9fafb;
        }

        /* Label styling */
        .form-label {
            color: #374151;
        }

        .dark .form-label {
            color: #d1d5db;
        }

        body>div>div.solid-card.rounded-xl.overflow-hidden>div.solid-card.border-t.p-4>div>nav>div.hidden.sm\:flex-1.sm\:flex.sm\:items-center.sm\:justify-between>div:nth-child(1)>p {
            margin-right: 10px;
        }

        /* Reduce motion */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body class="simple-bg min-h-screen" data-theme="{{ $theme }}">
    <div class="container mx-auto p-6">
        <!-- Header -->
        <div class="solid-card rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list-alt text-white"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-blue-600">Scollio Logs</h1>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Theme Toggle -->
                    <button onclick="toggleTheme()" class="solid-card p-3 rounded-lg btn-hover">
                        <i class="fas fa-moon dark:hidden text-gray-600"></i>
                        <i class="fas fa-sun hidden dark:block text-yellow-400"></i>
                    </button>

                    <!-- Clear Logs Button -->
                    <form method="POST" action="{{ route('scollio-logs.clear') }}">
                        @csrf
                        <button
                            class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white rounded-lg btn-hover flex items-center space-x-2"
                            onclick="return confirm('Are you sure you want to clear all logs?')">
                            <i class="fas fa-trash-alt"></i>
                            <span>Clear Logs</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="solid-card rounded-xl p-6 mb-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-8 gap-4">
                    <!-- Level Filter -->
                    <div>
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-layer-group mr-2"></i>Level
                        </label>
                        <select name="level" id="levelFilter" class="w-full form-input p-3 rounded-lg">
                            <option value="">All levels</option>
                            @foreach ($levels as $lvl)
                            <option value="{{ $lvl }}" {{ request('level') == $lvl ? 'selected' : '' }}>
                                {{ ucfirst($lvl) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Channel Filter -->
                    <div>
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-broadcast-tower mr-2"></i>Channel
                        </label>
                        <select name="channel" id="channelFilter" class="w-full form-input p-3 rounded-lg">
                            <option value="">All channels</option>
                            @foreach ($channels as $ch)
                            <option value="{{ $ch }}" {{ request('channel') == $ch ? 'selected' : '' }}>
                                {{ $ch }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-calendar mr-2"></i>From Date
                        </label>
                        <input type="date" name="from_date" id="fromDateFilter" value="{{ request('from_date') }}"
                            class="w-full form-input p-3 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-calendar mr-2"></i>To Date
                        </label>
                        <input type="date" name="to_date" id="toDateFilter" value="{{ request('to_date') }}"
                            class="w-full form-input p-3 rounded-lg">
                    </div>

                    <!-- Time Range -->
                    <div>
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-clock mr-2"></i>From Time
                        </label>
                        <input type="time" name="from_time" id="fromTimeFilter" value="{{ request('from_time') }}"
                            class="w-full form-input p-3 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-clock mr-2"></i>To Time
                        </label>
                        <input type="time" name="to_time" id="toTimeFilter" value="{{ request('to_time') }}"
                            class="w-full form-input p-3 rounded-lg">
                    </div>
                    <div></div>
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="resetFilters()"
                            class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg btn-hover flex items-center space-x-2">
                            <i class="fas fa-undo"></i>
                            <span>Reset</span>
                        </button>
                    </div>

                    <!-- Location Filter -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-map-marker-alt mr-2"></i>Location
                        </label>
                        <input type="text" name="location" id="locationFilter" placeholder="Search location..."
                            value="{{ request('location') }}" class="w-full form-input p-3 rounded-lg">
                    </div>

                    <!-- Search Filter -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium mb-2 form-label">
                            <i class="fas fa-search mr-2"></i>Search
                        </label>
                        <input type="text" name="q" id="searchFilter" placeholder="Search messages..."
                            value="{{ request('q') }}" class="w-full form-input p-3 rounded-lg">
                    </div>
                </div>


            </form>
        </div>

        <!-- Status Message -->
        @if (session('status'))
        <div
            class="solid-card rounded-xl p-4 mb-6 bg-green-50 dark:bg-green-900 border-green-200 dark:border-green-700">
            <div class="flex items-center space-x-3">
                <i class="fas fa-check-circle text-green-600"></i>
                <span class="text-green-800 dark:text-green-200">{{ session('status') }}</span>
            </div>
        </div>
        @endif

        <!-- Single Log View -->
        @if (isset($showSingle) && $showSingle === true && isset($log))
        <div class="solid-card rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold flex items-center space-x-3 text-primary">
                    <i class="fas fa-file-alt text-blue-600"></i>
                    <span>Log #{{ $log->id }}</span>
                </h2>
                <a href="{{ route('scollio-logs.index') }}"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg btn-hover flex items-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back</span>
                </a>
            </div>
            <div class="space-y-6">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-layer-group text-secondary"></i>
                    <strong class="text-primary">Level:</strong>
                    <span
                        class="px-4 py-2 rounded-full text-sm font-medium {{ $colors[$log->level] ?? 'bg-gray-200 text-black' }} flex items-center space-x-2">
                        @php
                        $levelIcons = [
                        'emergency' => 'fas fa-exclamation-triangle',
                        'alert' => 'fas fa-exclamation-circle',
                        'critical' => 'fas fa-times-circle',
                        'error' => 'fas fa-bug',
                        'warning' => 'fas fa-exclamation',
                        'notice' => 'fas fa-info-circle',
                        'info' => 'fas fa-info',
                        'debug' => 'fas fa-code',
                        ];
                        @endphp
                        <i class="{{ $levelIcons[$log->level] ?? 'fas fa-circle' }}"></i>
                        <span>{{ ucfirst($log->level) }}</span>
                    </span>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-comment-alt text-secondary mt-1"></i>
                    <div>
                        <strong class="text-primary">Message:</strong>
                        <p class="mt-1 text-primary">{{ $log->message }}</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-code text-secondary mt-1"></i>
                    <div class="flex-1">
                        <strong class="text-primary">Context:</strong>
                        <pre class="mt-2 solid-card p-4 rounded-lg text-sm overflow-x-auto text-primary">{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- Logs Table -->
        <div class="solid-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-hashtag mr-2"></i>ID
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-layer-group mr-2"></i>Level
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-comment-alt mr-2"></i>Message
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-map-marker-alt mr-2"></i>Location
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-broadcast-tower mr-2"></i>Channel
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-clock mr-2"></i>When
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold header-text">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($logs as $log)
                        <tr class="table-row log-row" data-level="{{ strtolower($log->level) }}"
                            data-channel="{{ strtolower($log->channel) }}"
                            data-location="{{ strtolower($log->location) }}"
                            data-message="{{ strtolower($log->message) }}"
                            data-date="{{ $log->created_at->format('Y-m-d') }}"
                            data-time="{{ $log->created_at->format('H:i') }}">
                            <td class="px-6 py-4 text-sm font-mono text-primary">{{ $log->id }}</td>
                            <td class="px-6 py-4 text-sm font-mono text-primary">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium {{ $colors[$log->level] ?? 'bg-gray-200 text-black' }} inline-flex items-center space-x-1 w-full justify-center">
                                    @php
                                    $levelIcons = [
                                    'emergency' => 'fas fa-exclamation-triangle',
                                    'alert' => 'fas fa-exclamation-circle',
                                    'critical' => 'fas fa-times-circle',
                                    'error' => 'fas fa-bug',
                                    'warning' => 'fas fa-exclamation',
                                    'notice' => 'fas fa-info-circle',
                                    'info' => 'fas fa-info',
                                    'debug' => 'fas fa-code',
                                    ];
                                    @endphp
                                    <i class="{{ $levelIcons[$log->level] ?? 'fas fa-circle' }}"></i>
                                    <span>{{ ucfirst($log->level) }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm max-w-xs truncate text-primary"
                                title="{{ $log->message }}">
                                {{ Str::limit($log->message, 80) }}
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-blue">
                                {{ $log->location }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs text-primary">{{ $log->channel }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-secondary">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('scollio-logs.show', $log->id) }}"
                                        class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded btn-hover text-xs">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST"
                                        action="{{ route('scollio-logs.delete', $log->id) }}"
                                        style="display:inline-block"
                                        onsubmit="return confirm('Are you sure?')">
                                        @method('DELETE')
                                        @csrf
                                        <button
                                            class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded btn-hover text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            @if ($logs->hasPages())
            <div class="solid-card border-t p-4">
                <div class="flex justify-center">
                    {{ $logs->links() }}
                </div>
            </div>
            @endif
        </div>
        @endif
    </div>

    <script>
        // Theme toggle
        const html = document.documentElement;

        function toggleTheme() {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        // Initialize theme
        (function() {
            const configTheme = document.body.dataset.theme;
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            let shouldBeDark = false;

            if (configTheme === 'dark') {
                shouldBeDark = true;
            } else if (configTheme === 'light') {
                shouldBeDark = false;
            } else if (configTheme === 'auto') {
                shouldBeDark = savedTheme === 'dark' || (!savedTheme && prefersDark);
            }

            if (shouldBeDark) {
                html.classList.add('dark');
            }
        })();

        // Optimized debounce
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func.apply(this, args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Fixed filtering function
        function filterLogs() {
            const level = document.getElementById('levelFilter').value.toLowerCase();
            const channel = document.getElementById('channelFilter').value.toLowerCase();
            const location = document.getElementById('locationFilter').value.toLowerCase();
            const search = document.getElementById('searchFilter').value.toLowerCase();

            // Add these new filter values
            const fromDate = document.getElementById('fromDateFilter').value;
            const toDate = document.getElementById('toDateFilter').value;
            const fromTime = document.getElementById('fromTimeFilter').value;
            const toTime = document.getElementById('toTimeFilter').value;

            const rows = document.querySelectorAll('.log-row');

            rows.forEach(row => {
                const rowLevel = row.dataset.level;
                const rowChannel = row.dataset.channel;
                const rowLocation = row.dataset.location;
                const rowMessage = row.dataset.message;
                const rowDate = row.dataset.date;
                const rowTime = row.dataset.time; // You'll need to add this data attribute

                const matches =
                    (!level || rowLevel === level || rowLevel.includes(level)) &&
                    (!channel || rowChannel === channel || rowChannel.includes(channel)) &&
                    (!location || rowLocation.includes(location)) &&
                    (!search || rowMessage.includes(search)) &&
                    (!fromDate || rowDate >= fromDate) &&
                    (!toDate || rowDate <= toDate) &&
                    (!fromTime || rowTime >= fromTime) &&
                    (!toTime || rowTime <= toTime);

                if (matches) {
                    row.classList.remove('filtered-hidden');
                } else {
                    row.classList.add('filtered-hidden');
                }
            });
        }

        // Create debounced filter
        const debouncedFilter = debounce(filterLogs, 200);

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Direct event listeners for better reliability
            document.getElementById('levelFilter').addEventListener('change', filterLogs);
            document.getElementById('channelFilter').addEventListener('change', filterLogs);
            document.getElementById('locationFilter').addEventListener('input', debouncedFilter);
            document.getElementById('searchFilter').addEventListener('input', debouncedFilter);

            document.getElementById('fromDateFilter').addEventListener('change', filterLogs);
            document.getElementById('toDateFilter').addEventListener('change', filterLogs);
            document.getElementById('fromTimeFilter').addEventListener('change', filterLogs);
            document.getElementById('toTimeFilter').addEventListener('change', filterLogs);
        });

        // Reset filters
        function resetFilters() {
            document.getElementById('levelFilter').value = '';
            document.getElementById('channelFilter').value = '';
            document.getElementById('locationFilter').value = '';
            document.getElementById('searchFilter').value = '';
            document.getElementById('fromDateFilter').value = '';
            document.getElementById('toDateFilter').value = '';
            document.getElementById('fromTimeFilter').value = '';
            document.getElementById('toTimeFilter').value = '';

            // Show all rows
            document.querySelectorAll('.filtered-hidden').forEach(row => {
                row.classList.remove('filtered-hidden');
            });
        }
    </script>
</body>

</html>