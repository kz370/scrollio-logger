<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scollio Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Keep all your existing styles */
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

        .simple-bg {
            background: #f8fafc;
        }

        .dark .simple-bg {
            background: #111827;
        }

        .btn-hover {
            transition: transform 0.15s ease;
            will-change: transform;
        }

        .btn-hover:hover {
            transform: translateY(-1px);
        }

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

        .table-row {
            transition: background-color 0.1s ease;
        }

        .table-row:hover {
            background-color: #f9fafb;
        }

        .dark .table-row:hover {
            background-color: #374151;
        }

        .header-text {
            color: #1f2937;
        }

        .dark .header-text {
            color: black;
        }

        .dark .channel-text {
            background-color: #374151;
        }

        .form-label {
            color: #374151;
        }

        .dark .form-label {
            color: #d1d5db;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loading-overlay {
            position: relative;
        }

        .loading-overlay.is-loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
        }

        .dark .loading-overlay.is-loading::after {
            background: rgba(31, 41, 55, 0.8);
        }

        /* Live updates styles */
        .live-indicator {
            position: relative;
        }

        .live-indicator.active::before {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .new-log-row {
            animation: highlight 3s ease-in-out;
        }

        @keyframes highlight {
            0% {
                background-color: #fef3c7;
            }

            100% {
                background-color: transparent;
            }
        }

        .dark .new-log-row {
            animation: highlight-dark 3s ease-in-out;
        }

        @keyframes highlight-dark {
            0% {
                background-color: #374151;
            }

            100% {
                background-color: transparent;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        #paginationContainer nav p {
            margin-right: 10px;
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
                    <h1 class="text-3xl font-bold text-blue-600">{{ config('scollio-logger.title') }}</h1>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Live Updates Toggle -->
                    @if (!isset($showSingle) || $showSingle !== true)
                        <button onclick="toggleLiveUpdates()" id="liveToggleBtn"
                            class="solid-card p-3 rounded-lg btn-hover live-indicator flex items-center space-x-2">
                            <i class="fas fa-wifi text-gray-600 dark:text-gray-300" id="liveIcon"></i>
                            <span class="text-sm text-gray-600 dark:text-gray-300" id="liveText">Live</span>
                        </button>
                    @endif

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

            <!-- Live Updates Status -->
            @if (!isset($showSingle) || $showSingle !== true)
                <div id="liveStatus" class="mt-4 text-sm text-secondary hidden">
                    <div class="flex items-center space-x-2">
                        <div class="loading-spinner w-4 h-4"></div>
                        <span>Live updates enabled - refreshing every <span id="refreshInterval">3</span> seconds</span>
                        <span class="text-xs">| Last update: <span id="lastUpdate">Never</span></span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Filters Section -->
        @if (!isset($showSingle) || $showSingle !== true)
            <div class="solid-card rounded-xl p-6 mb-6">
                <form id="filtersForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-8 gap-4">
                        <!-- Level Filter -->
                        <div>
                            <label class="block text-sm font-medium mb-2 form-label">
                                <i class="fas fa-layer-group mr-2"></i>Level
                            </label>
                            <select name="level" id="levelFilter" class="w-full form-input p-3 rounded-lg">
                                <option value="">All levels</option>
                                @foreach ($levels as $lvl)
                                    <option value="{{ $lvl }}"
                                        {{ request('level') == $lvl ? 'selected' : '' }}>
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
                                    <option value="{{ $ch }}"
                                        {{ request('channel') == $ch ? 'selected' : '' }}>
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
                            <input type="date" name="from_date" id="fromDateFilter"
                                value="{{ request('from_date') }}" class="w-full form-input p-3 rounded-lg">
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
                            <input type="time" name="from_time" id="fromTimeFilter"
                                value="{{ request('from_time') }}" class="w-full form-input p-3 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 form-label">
                                <i class="fas fa-clock mr-2"></i>To Time
                            </label>
                            <input type="time" name="to_time" id="toTimeFilter" value="{{ request('to_time') }}"
                                class="w-full form-input p-3 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2 form-label">
                                <i class="fas fa-sync mr-2"></i>Refresh Rate
                            </label>
                            <select id="refreshRateSelect" class="w-full form-input p-3 rounded-lg">
                                <option value="3" selected>3 seconds</option>
                                <option value="5">5 seconds</option>
                                <option value="10">10 seconds</option>
                                <option value="30">30 seconds</option>
                            </select>
                        </div>

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
                            <input type="text" name="location" id="locationFilter"
                                placeholder="Search location..." value="{{ request('location') }}"
                                class="w-full form-input p-3 rounded-lg">
                        </div>

                        <!-- Search Filter -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium mb-2 form-label">
                                <i class="fas fa-search mr-2"></i>Search
                            </label>
                            <input type="text" name="q" id="searchFilter" placeholder="Search messages..."
                                value="{{ request('q') }}" class="w-full form-input p-3 rounded-lg">
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button onclick="performSearch(1, true)"
                                class="px-6 py-3 bg-green-500 hover:bg-gray-600 text-white rounded-lg btn-hover flex items-center space-x-2">
                                <i class="fas fa-undo"></i>
                                <span>Refresh</span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Results Info -->
                <div id="resultsInfo" class="mt-4 text-sm text-secondary">
                    @if ($logs->total())
                        Showing {{ $logs->firstItem() }}-{{ $logs->lastItem() }} of {{ $logs->total() }} results
                    @endif
                </div>
            </div>
        @endif

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
            <div class="solid-card rounded-xl p-6 space-y-6">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold flex items-center space-x-3 text-primary">
                        <i class="fas fa-file-alt text-blue-600"></i>
                        <span>Log #{{ $log->id }}</span>
                    </h2>
                    <a href="{{ route('scollio-logs.index') }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center space-x-2">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-clock text-secondary"></i>
                        <div>
                            <strong class="text-primary">Time:</strong>
                            <div class="text-primary">{{ $log->created_at->format('Y-m-d H:i:s A') }}</div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <i class="fas fa-layer-group text-secondary"></i>
                        <div>
                            <strong class="text-primary">Level:</strong>
                            <div
                                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $colors[$log->level] ?? 'bg-gray-200 text-black' }} space-x-2">
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
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <i class="fas fa-map-marker-alt text-secondary"></i>
                        <div>
                            <strong class="text-primary">Location:</strong>
                            <div class="text-primary">{{ $log->location }}</div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <i class="fas fa-broadcast-tower text-secondary"></i>
                        <div>
                            <strong class="text-primary">Channel:</strong>
                            <div class="text-primary">{{ $log->channel }}</div>
                        </div>
                    </div>

                </div>

                <div class="space-y-4">
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
                            <pre class="mt-2 solid-card p-4 rounded-lg text-sm overflow-x-auto text-primary">{{ str_replace('\/', '/', json_encode($log->context, JSON_PRETTY_PRINT)) }}</pre>
                        </div>
                    </div>
                </div>

            </div>
        @else
            <!-- Logs Table -->
            <div class="solid-card rounded-xl overflow-hidden loading-overlay" id="logsContainer">
                <div id="logsTableContent">
                    @include('scollio-logger::partials.logs-table', ['logs' => $logs, 'colors' => $colors])
                </div>

                <!-- Pagination -->
                <div id="paginationContainer" class="solid-card border-t p-4">
                    @if ($logs->hasPages())
                        <div class="flex justify-center">
                            {{ $logs->links() }}
                        </div>
                    @endif
                </div>
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

        // AJAX filtering and live updates functionality
        const form = document.getElementById('filtersForm');
        const logsContainer = document.getElementById('logsContainer');
        const logsTableContent = document.getElementById('logsTableContent');
        const paginationContainer = document.getElementById('paginationContainer');
        const resultsInfo = document.getElementById('resultsInfo');
        const liveToggleBtn = document.getElementById('liveToggleBtn');
        const liveStatus = document.getElementById('liveStatus');
        const liveIcon = document.getElementById('liveIcon');
        const liveText = document.getElementById('liveText');
        const refreshRateSelect = document.getElementById('refreshRateSelect');
        const refreshInterval = document.getElementById('refreshInterval');
        const lastUpdate = document.getElementById('lastUpdate');

        let currentRequest = null;
        let liveUpdatesEnabled = false;
        let liveUpdateInterval = null;
        let currentRefreshRate = 3; // seconds
        let lastLogId = null;
        let currentPage = 1;

        if (refreshRateSelect && refreshInterval) {
            const selectedValue = refreshRateSelect.value;
            currentRefreshRate = parseInt(selectedValue);
            refreshInterval.textContent = currentRefreshRate;
        }

        // Initialize last log ID from current data
        function initializeLastLogId() {
            const firstRow = document.querySelector('#logsTableContent tbody tr:first-child td:first-child');
            if (firstRow && firstRow.textContent.trim()) {
                lastLogId = parseInt(firstRow.textContent.trim(), 10);
                console.log(`Initialized lastLogId to: ${lastLogId}`); // Debug log
            } else {
                lastLogId = 0; // Default to 0 if no logs
            }
        }

        function showLoading() {
            if (logsContainer) {
                logsContainer.classList.add('is-loading');
            }
        }

        function hideLoading() {
            if (logsContainer) {
                logsContainer.classList.remove('is-loading');
            }
        }

        function updateTable(data, highlightNew = false) {
            if (logsTableContent && data.html) {
                // Parse the new HTML to get row data before updating DOM
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;
                const newRows = tempDiv.querySelectorAll('tbody tr');

                // Store current lastLogId before updating
                const previousLastLogId = lastLogId;

                // Update table content
                logsTableContent.innerHTML = data.html;

                // Only highlight if this is a live update and we have a previous reference
                if (highlightNew && previousLastLogId && newRows.length > 0) {
                    // Get the actual DOM rows after innerHTML update
                    const currentRows = document.querySelectorAll('#logsTableContent tbody tr');

                    currentRows.forEach(row => {
                        const firstCell = row.querySelector('td:first-child');
                        if (firstCell && firstCell.textContent.trim()) {
                            const rowId = parseInt(firstCell.textContent.trim(), 10);

                            // Only highlight if this row's ID is greater than the previous highest ID
                            if (rowId > previousLastLogId) {
                                row.classList.add('new-log-row');
                                console.log(`Highlighting new log ID: ${rowId}`); // Debug log
                            }
                        }
                    });
                }

                // Update lastLogId to the current highest log ID (first row)
                const firstRow = document.querySelector('#logsTableContent tbody tr:first-child td:first-child');
                if (firstRow && firstRow.textContent.trim()) {
                    lastLogId = parseInt(firstRow.textContent.trim(), 10);
                }
            }

            // Handle pagination updates
            if (paginationContainer) {
                if (data.pagination) {
                    paginationContainer.innerHTML = '<div class="flex justify-center">' + data.pagination + '</div>';
                    paginationContainer.style.display = 'block';
                    setupPaginationListeners();
                } else {
                    paginationContainer.style.display = 'none';
                }
            }

            // Handle results info updates
            if (resultsInfo && data.showing) {
                if (data.showing.total > 0) {
                    resultsInfo.innerHTML =
                        `Showing ${data.showing.from}-${data.showing.to} of ${data.showing.total} results`;
                } else {
                    resultsInfo.innerHTML = 'No results found';
                }
            }
        }


        function performSearch(page = 1, isLiveUpdate = false) {
            // Cancel previous request if still pending
            if (currentRequest && currentRequest.readyState !== 4) {
                currentRequest.abort();
            }

            const formData = new FormData(form);
            formData.append('page', page);

            const params = new URLSearchParams(formData).toString();
            const url = `{{ route('scollio-logs.index') }}?${params}`;

            if (!isLiveUpdate) {
                showLoading();
            }
            currentPage = page;

            currentRequest = new XMLHttpRequest();
            currentRequest.open('GET', url, true);
            currentRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            currentRequest.setRequestHeader('Accept', 'application/json');

            currentRequest.onreadystatechange = function() {
                if (this.readyState === 4) {
                    if (!isLiveUpdate) {
                        hideLoading();
                    }

                    if (this.status === 200) {
                        try {
                            const data = JSON.parse(this.responseText);
                            if (data.success) {
                                updateTable(data, isLiveUpdate);

                                // Update URL without reload (only for user interactions, not live updates)
                                if (!isLiveUpdate) {
                                    const newUrl = new URL(window.location);
                                    const urlParams = new URLSearchParams(formData);
                                    newUrl.search = urlParams.toString();
                                    window.history.pushState({}, '', newUrl);
                                }

                                // Update last update time
                                if (lastUpdate) {
                                    lastUpdate.textContent = new Date().toLocaleTimeString();
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                    } else {
                        console.error('Request failed with status:', this.status);
                    }
                }
            };

            currentRequest.send();
        }

        function setupPaginationListeners() {
            // Setup pagination click listeners
            document.querySelectorAll('#paginationContainer a').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page') || 1;
                    performSearch(page);
                });
            });
        }

        function toggleLiveUpdates() {
            liveUpdatesEnabled = !liveUpdatesEnabled;

            if (liveUpdatesEnabled) {
                // Enable live updates
                liveToggleBtn.classList.add('active');
                liveIcon.className = 'fas fa-wifi text-green-500';
                liveText.textContent = 'Live';
                liveText.className = 'text-sm text-green-500';
                liveStatus.classList.remove('hidden');

                // Always go to page 1 when enabling live updates
                currentPage = 1;
                performSearch(1);

                // Start interval
                startLiveUpdates();
            } else {
                // Disable live updates
                liveToggleBtn.classList.remove('active');
                liveIcon.className = 'fas fa-wifi text-gray-600 dark:text-gray-300';
                liveText.textContent = 'Live';
                liveText.className = 'text-sm text-gray-600 dark:text-gray-300';
                liveStatus.classList.add('hidden');

                // Stop interval
                stopLiveUpdates();
            }
        }

        function startLiveUpdates() {
            if (liveUpdateInterval) {
                clearInterval(liveUpdateInterval);
            }

            liveUpdateInterval = setInterval(() => {
                if (liveUpdatesEnabled && currentPage === 1) { // Only update if on first page
                    performSearch(1, true);
                }
            }, currentRefreshRate * 1000);
        }

        function stopLiveUpdates() {
            if (liveUpdateInterval) {
                clearInterval(liveUpdateInterval);
                liveUpdateInterval = null;
            }
        }

        // Debounce function
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        if (form) {
            // Auto-submit for select/date/time inputs
            ['levelFilter', 'channelFilter', 'fromDateFilter', 'toDateFilter', 'fromTimeFilter', 'toTimeFilter'].forEach(
                id => {
                    const element = document.getElementById(id);
                    if (element) {
                        element.addEventListener('change', () => {
                            currentPage = 1; // Reset to first page when filtering
                            performSearch();
                        });
                    }
                });

            // Debounced submit for text inputs
            ['locationFilter', 'searchFilter'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', debounce(() => {
                        currentPage = 1; // Reset to first page when searching
                        performSearch();
                    }, 400));
                }
            });

            // Setup initial pagination listeners
            setupPaginationListeners();

            // Initialize last log ID
            initializeLastLogId();
        }

        // Refresh rate change handler
        if (refreshRateSelect) {
            refreshRateSelect.addEventListener('change', function() {
                currentRefreshRate = parseInt(this.value);
                refreshInterval.textContent = currentRefreshRate;

                // Restart live updates with new interval
                if (liveUpdatesEnabled) {
                    startLiveUpdates();
                }
            });
        }

        // Reset filters
        function resetFilters() {
            if (form) {
                form.reset();
                currentPage = 1;
                performSearch();
            }
        }

        // Handle page visibility change (pause live updates when tab is hidden)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                if (liveUpdatesEnabled) {
                    stopLiveUpdates();
                }
            } else {
                if (liveUpdatesEnabled) {
                    startLiveUpdates();
                    // Immediately update when tab becomes visible again
                    performSearch(currentPage, true);
                }
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            stopLiveUpdates();
        });
    </script>
</body>

</html>
