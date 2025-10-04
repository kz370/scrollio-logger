<div class="overflow-y-auto max-h-full">
    <table class="min-w-full text-sm border-separate border-spacing-y-2">
        <thead class="sticky top-0 z-10 bg-gray-100 dark:bg-gray-800 shadow-sm">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                    <i class="fas fa-hashtag mr-2"></i>ID
                </th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 w-48">
                    <i class="fas fa-code mr-2"></i>Status
                </th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                    <i class="fas fa-link mr-2"></i>URL
                </th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                    <i class="fas fa-clock mr-2"></i>Time
                </th>
                <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Error
                </th>
                <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">
                    <i class="fas fa-cogs mr-2"></i>Actions
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($logs as $log)
                @php
                    $status = (int) ($log->status_code ?? 0);

                    $color = match (true) {
                        $status >= 100 && $status < 200 => 'bg-gray-100 text-gray-700 border border-gray-300 dark:bg-gray-700 dark:text-gray-200',
                        $status >= 200 && $status < 300 => 'bg-green-100 text-green-700 border border-green-300 dark:bg-green-800 dark:text-green-200',
                        $status >= 300 && $status < 400 => 'bg-blue-100 text-blue-700 border border-blue-300 dark:bg-blue-800 dark:text-blue-200',
                        $status >= 400 && $status < 500 => 'bg-yellow-100 text-yellow-800 border border-yellow-300 dark:bg-yellow-800 dark:text-yellow-200',
                        $status >= 500 && $status < 600 => 'bg-red-100 text-red-700 border border-red-300 dark:bg-red-800 dark:text-red-200',
                        default => 'bg-gray-100 text-gray-700 border border-gray-300 dark:bg-gray-700 dark:text-gray-200',
                    };

                    $statusName = match ($status) {
                        100 => 'Continue',
                        101 => 'Switching Protocols',
                        102 => 'Processing',
                        200 => 'OK',
                        201 => 'Created',
                        202 => 'Accepted',
                        203 => 'Non-Authoritative',
                        204 => 'No Content',
                        205 => 'Reset Content',
                        206 => 'Partial Content',
                        300 => 'Multiple Choices',
                        301 => 'Moved Permanently',
                        302 => 'Found',
                        303 => 'See Other',
                        304 => 'Not Modified',
                        307 => 'Temp Redirect',
                        308 => 'Perm Redirect',
                        400 => 'Bad Request',
                        401 => 'Unauthorized',
                        403 => 'Forbidden',
                        404 => 'Not Found',
                        405 => 'Not Allowed',
                        408 => 'Timeout',
                        409 => 'Conflict',
                        410 => 'Gone',
                        413 => 'Too Large',
                        414 => 'URI Too Long',
                        415 => 'Bad Type',
                        418 => "I'm a Teapot",
                        422 => 'Unprocessable',
                        429 => 'Too Many',
                        500 => 'Server Error',
                        501 => 'Not Implemented',
                        502 => 'Bad Gateway',
                        503 => 'Unavailable',
                        504 => 'Timeout',
                        default => 'Unknown',
                    };

                    $icon = match (true) {
                        $status >= 500 => '❌',
                        $status >= 400 => '⚠️',
                        $status >= 300 => '🔁',
                        $status >= 200 => '✅',
                        default => 'ℹ️',
                    };
                @endphp

                <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 shadow-sm rounded-lg transition"
                    data-log-id="{{ $log->id }}">
                    <!-- ID -->
                    <td class="px-4 py-3 font-mono text-blue-600 dark:text-blue-400">
                        {{ $log->id }}
                    </td>

                    <!-- Combined Status (clickable, icon, tooltip, fixed width) -->
                    <td class="px-4 py-3 text-center w-48">
                        <button 
                            title="{{ $status }} {{ $statusName }}"
                            onclick="filterByStatus({{ $status }})"
                            class="inline-flex items-center justify-center gap-2 px-4 py-1.5 text-xs font-semibold rounded-full {{ $color }} w-[140px] h-[28px] hover:opacity-90 transition"
                            style="min-width: 160px;">
                            <span>{{ $icon }}</span>
                            <span class="font-bold">{{ $status }}</span>
                            <span class="truncate">{{ $statusName }}</span>
                        </button>
                    </td>

                    <!-- URL -->
                    <td class="px-4 py-3 truncate max-w-xs text-gray-700 dark:text-gray-200"
                        title="{{ $log->url }}">
                        {{ Str::limit(Str::after($log->url, config('app.url')), 80) }}
                    </td>

                    <!-- Time -->
                    <td class="px-4 py-3 whitespace-nowrap text-gray-500 dark:text-gray-400">
                        {{ $log->requested_at ? $log->requested_at->diffForHumans() : $log->created_at->diffForHumans() }}
                    </td>

                    <!-- Error -->
                    <td class="px-4 py-3 text-red-500 dark:text-red-400 truncate max-w-sm"
                        title="{{ $log->exception_message }}">
                        {{ Str::limit($log->exception_message, 60) }}
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-3 text-center">
                        <div class="inline-flex items-center space-x-2">
                            <a href="{{ route('scollio-logs.show', $log->id) }}"
                                class="p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg btn-hover transition"
                                title="View details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button
                                class="delete-log-btn p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg btn-hover transition"
                                data-url="{{ route('scollio-logs.delete', $log->id) }}" title="Delete log">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-3 block opacity-50"></i>
                        <p>No logs found.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function filterByStatus(code) {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.value = code;
        const inputEvent = new Event('input', { bubbles: true });
        statusFilter.dispatchEvent(inputEvent);
    }
}
</script>
