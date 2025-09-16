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
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($logs as $log)
            <tr class="table-row">
                <td class="px-6 py-4 text-sm font-mono text-primary">{{ $log->id }}</td>
                <td class="px-6 py-4 text-sm font-mono text-primary">
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $colors[$log->level] ?? 'bg-gray-200 text-black' }} inline-flex items-center space-x-1 w-full justify-center">
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
                <td class="px-6 py-4 text-sm max-w-xs truncate text-primary" title="{{ $log->message }}">
                    {{ Str::limit($log->message, 80) }}
                </td>
                <td class="px-6 py-4 text-sm font-mono text-blue">{{ $log->location }}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-xs text-primary channel-text">{{ $log->channel }}</span>
                </td>
                <td class="px-6 py-4 text-sm text-secondary">{{ $log->created_at->diffForHumans() }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('scollio-logs.show', $log->id) }}" class="px-3 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded btn-hover text-xs">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('scollio-logs.delete', $log->id) }}" style="display:inline-block" onsubmit="return confirm('Are you sure?')">
                            @method('DELETE')
                            @csrf
                            <button class="px-3 py-2 bg-red-500 hover:bg-red-600 text-white rounded btn-hover text-xs">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-secondary">
                    <i class="fas fa-inbox text-4xl mb-4 block"></i>
                    <p>No logs found matching your criteria.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
