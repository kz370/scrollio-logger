<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scollio Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        body {
            background: #f9fafb;
        }

        .dark body {
            background: #111827;
        }

        .app-container {
            display: flex;
            flex-direction: column;
            height: 100vh;
            padding: 1.5rem;
            gap: 1rem;
        }

        .solid-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .dark .solid-card {
            background: #1f2937;
            border: 1px solid #374151;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        .header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
        }

        .btn-hover {
            transition: all 0.15s ease;
        }

        .btn-hover:hover {
            transform: translateY(-1px);
        }

        .logs-area {
            flex: 1;
            overflow-y: auto;
            border-radius: 0.75rem;
        }

        .form-input {
            color: #374151 !important;
            background: #fff !important;
            border: 1px solid #d1d5db;
        }

        .dark .form-input {
            color: #f9fafb !important;
            background: #374151 !important;
            border: 1px solid #4b5563;
        }

        .header-text {
            color: #4b5563;
        }

        .dark .header-text {
            color: #d1d5db;
        }

        #paginationContainer p {
            margin-right: 10px;
        }

        /* Toggle */
        .toggle {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .toggle-input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-track {
            width: 44px;
            height: 24px;
            background: #d1d5db;
            border-radius: 9999px;
            position: relative;
            transition: background-color 0.2s ease;
        }

        .dark .toggle-track {
            background: #374151;
        }

        .toggle-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: #ffffff;
            border-radius: 9999px;
            transition: transform 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        }

        .toggle-input:checked+.toggle-track {
            background: #10b981;
        }

        .toggle-input:checked+.toggle-track .toggle-thumb {
            transform: translateX(20px);
        }

        /* Dropdown */
        .dropdown-item {
            padding: 0.4rem 0.75rem;
            cursor: pointer;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            white-space: nowrap;
            gap: 0.75rem;
            transition: background 0.15s ease;
        }

        .dropdown-item:hover {
            background: #f3f4f6;
        }

        .dark .dropdown-item:hover {
            background: #4b5563;
        }

        .dropdown-item span:first-child {
            text-align: right;
            flex-shrink: 0;
        }

        .dropdown-item span:last-child {
            flex: 1;
            text-align: left;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .code-gray {
            color: #6b7280;
        }

        .code-green {
            color: #047857;
        }

        .code-blue {
            color: #2563eb;
        }

        .code-yellow {
            color: #b45309;
        }

        .code-red {
            color: #dc2626;
        }

        .dark .code-gray {
            color: #9ca3af;
        }

        .dark .code-green {
            color: #6ee7b7;
        }

        .dark .code-blue {
            color: #60a5fa;
        }

        .dark .code-yellow {
            color: #facc15;
        }

        .dark .code-red {
            color: #f87171;
        }

        #updateIndicator {
            display: none;
        }

        #updateIndicator.active {
            display: inline-block;
            animation: pulse 1.2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 0.5;
            }

            50% {
                opacity: 1;
            }

            100% {
                opacity: 0.5;
            }
        }
    </style>
</head>

<body class="simple-bg" data-theme="auto">
    <div class="app-container">

        <!-- Header -->
        <div class="solid-card header-bar">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-list-alt text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ config('scollio-logger.title') }}
                </h1>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Live Logs Toggle -->
                <div class="flex items-center space-x-2">
                    <label for="liveToggle" class="text-sm font-medium header-text">Realtime</label>
                    <label class="toggle cursor-pointer">
                        <input type="checkbox" id="liveToggle" class="toggle-input">
                        <span class="toggle-track">
                            <span class="toggle-thumb"></span>
                        </span>
                    </label>
                    <span id="updateIndicator" class="text-green-500 text-xs ml-2">
                        <i class="fas fa-sync-alt fa-spin mr-1"></i> Updating...
                    </span>
                </div>

                <!-- Theme Toggle -->
                <button onclick="toggleTheme()" class="solid-card p-3 rounded-lg btn-hover">
                    <i class="fas fa-moon dark:hidden text-gray-600"></i>
                    <i class="fas fa-sun hidden dark:block text-yellow-400"></i>
                </button>

                <!-- Clear Logs -->
                <button id="clearLogsBtn"
                    class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg btn-hover flex items-center space-x-2">
                    <i class="fas fa-trash-alt"></i><span>Clear</span>
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="solid-card rounded-xl p-4">
            <form id="filtersForm" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <input type="text" name="q" id="searchFilter" placeholder="Search URL or message"
                    class="form-input p-2.5 rounded-lg col-span-2" value="{{ request('q') }}">

                <!-- Status Code Dropdown -->
                <div class="relative col-span-1">
                    <input type="text" id="statusFilter" name="status_code"
                        placeholder="Search Status Code or Name"
                        class="form-input p-2.5 rounded-lg w-full" autocomplete="off">
                    <div id="statusDropdown"
                        class="absolute hidden bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg mt-1 w-full shadow-lg max-h-48 overflow-y-auto z-50">
                    </div>
                </div>

                <input type="date" name="from_date" id="fromDateFilter" class="form-input p-2.5 rounded-lg">
                <input type="date" name="to_date" id="toDateFilter" class="form-input p-2.5 rounded-lg">
                <button type="button" id="resetBtn"
                    class="px-5 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg"><i
                        class="fas fa-undo mr-2"></i>Reset</button>
            </form>
        </div>

        <!-- Logs Table -->
        <div class="solid-card rounded-xl flex flex-col overflow-hidden logs-area" id="logsContainer">
            <div id="logsTableContent" class="flex-1 overflow-y-auto">
                @include('scollio::partials.logs-table', ['logs' => $logs, 'colors' => $colors])
            </div>
            <div id="paginationContainer" class="solid-card border-t p-3">
                @if ($logs->hasPages())
                <div class="flex justify-center pagination-links">{!! $logs->links() !!}</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        const html = document.documentElement;
        const logsTable = document.getElementById('logsTableContent');
        const pagination = document.getElementById('paginationContainer');
        const form = document.getElementById('filtersForm');
        const clearBtn = document.getElementById('clearLogsBtn');
        const resetBtn = document.getElementById('resetBtn');
        const liveToggle = document.getElementById('liveToggle');
        const updateIndicator = document.getElementById('updateIndicator');
        const statusDropdown = document.getElementById('statusDropdown');
        const statusFilter = document.getElementById('statusFilter');

        let stopPolling = true;
        let lastId = 0;
        let currentPage = 1;

        // Dropdown setup
        const statusCodes = {
            100: "Continue",
            101: "Switching Protocols",
            102: "Processing",
            200: "OK",
            201: "Created",
            202: "Accepted",
            204: "No Content",
            301: "Moved Permanently",
            302: "Found",
            304: "Not Modified",
            400: "Bad Request",
            401: "Unauthorized",
            403: "Forbidden",
            404: "Not Found",
            405: "Method Not Allowed",
            408: "Request Timeout",
            409: "Conflict",
            422: "Unprocessable Entity",
            429: "Too Many Requests",
            500: "Internal Server Error",
            501: "Not Implemented",
            502: "Bad Gateway",
            503: "Service Unavailable",
            504: "Gateway Timeout"
        };

        const getColorClass = (code) => {
            if (code >= 100 && code < 200) return "code-gray";
            if (code >= 200 && code < 300) return "code-green";
            if (code >= 300 && code < 400) return "code-blue";
            if (code >= 400 && code < 500) return "code-yellow";
            if (code >= 500 && code < 600) return "code-red";
            return "code-gray";
        };

        Object.entries(statusCodes).forEach(([code, name]) => {
            const div = document.createElement('div');
            div.className = `dropdown-item ${getColorClass(code)}`;
            div.innerHTML = `
                <span class="font-mono">${code}</span>
                <span class="truncate">${name}</span>
            `;
            div.dataset.value = code;
            div.onclick = () => {
                statusFilter.value = code;
                statusDropdown.classList.add('hidden');
                performSearch();
            };
            statusDropdown.appendChild(div);
        });

        statusFilter.addEventListener('input', () => {
            const val = statusFilter.value.toLowerCase();
            statusDropdown.classList.remove('hidden');
            Array.from(statusDropdown.children).forEach(item => {
                item.style.display = item.textContent.toLowerCase().includes(val) ? 'flex' : 'none';
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('#statusDropdown') && !e.target.closest('#statusFilter')) {
                statusDropdown.classList.add('hidden');
            }
        });

        // Theme toggle
        function toggleTheme() {
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }

        (() => {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) html.classList.add('dark');
        })();

        // Filters and actions
        form.querySelectorAll('input').forEach(i => i.addEventListener('input', () => performSearch()));
        resetBtn.addEventListener('click', () => {
            form.reset();
            performSearch();
        });

        clearBtn.addEventListener('click', () => {
            if (!confirm('Are you sure you want to clear all logs?')) return;
            fetch(`{{ route('scollio-logs.clear') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(r => r.json()).then(d => {
                if (d.success) {
                    logsTable.innerHTML = '';
                    pagination.innerHTML = '';
                    lastId = 0;
                }
            });
        });

        function performSearch(page = 1) {
            currentPage = page;
            const fd = new FormData(form);
            fd.append('page', page);
            const url = `{{ route('scollio-logs.index') }}?${new URLSearchParams(fd).toString()}`;
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        logsTable.innerHTML = data.html;
                        const paginationLinks = pagination.querySelector('.pagination-links');
                        if (data.pagination) paginationLinks.innerHTML = data.pagination;
                        const firstRow = logsTable.querySelector('[data-log-id]');
                        if (firstRow) lastId = parseInt(firstRow.dataset.logId) || lastId;
                    }
                });
        }

        // Polling
        async function pollLogs() {
            if (stopPolling) return;
            updateIndicator.classList.add('active');

            try {
                const response = await fetch(`{{ route('scollio-logs.poll') }}?last_id=${lastId}&page=${currentPage}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (data.success) {
                    logsTable.innerHTML = data.html;
                    const paginationLinks = pagination.querySelector('.pagination-links');
                    if (data.pagination) paginationLinks.innerHTML = data.pagination;
                    lastId = data.last_id || lastId;
                }

                updateIndicator.classList.remove('active');
                pollLogs();
            } catch (error) {
                updateIndicator.classList.remove('active');
                setTimeout(() => pollLogs(), 2000);
            }
        }

        document.addEventListener('click', e => {
            const delBtn = e.target.closest('.delete-log-btn');
            if (delBtn) {
                e.preventDefault();
                if (!confirm('Delete this log?')) return;
                fetch(delBtn.dataset.url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(r => r.json()).then(d => {
                    if (d.success) performSearch(currentPage);
                });
            }

            const link = e.target.closest('#paginationContainer a');
            if (link) {
                e.preventDefault();
                const url = new URL(link.href);
                currentPage = url.searchParams.get('page') || 1;
                const wasLive = !stopPolling;
                stopPolling = true;
                performSearch(currentPage);
                if (wasLive) {
                    setTimeout(() => {
                        stopPolling = false;
                        pollLogs();
                    }, 500);
                }
            }
        });

        // Live toggle
        liveToggle.addEventListener('change', () => {
            if (liveToggle.checked) {
                stopPolling = false;
                pollLogs();
            } else {
                stopPolling = true;
                updateIndicator.classList.remove('active');
            }
        });
    </script>
</body>

</html>