<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scollio Log #{{ $log->id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
            /* ❌ disable page scroll */
            margin: 0;
            background: linear-gradient(to bottom right, #f9fafb, #eef2ff);
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }

        .page {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .card {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            width: 100%;
            max-width: 1100px;
            max-height: 95vh;
            /* fit viewport nicely */
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .card-content {
            flex: 1;
            overflow-y: auto;
            /* scroll only inside the card if necessary */
            padding: 2rem;
        }

        pre {
            background: #f3f4f6;
            border-radius: 0.75rem;
            padding: 1rem;
            overflow-x: auto;
            overflow-y: auto;
            font-size: 0.875rem;
            color: #111827;
            line-height: 1.5;
            max-height: 250px;
            /* independent scroll for long JSON */
        }

        summary {
            cursor: pointer;
            transition: color 0.2s ease;
        }

        summary:hover {
            color: #2563eb;
        }

        a.btn {
            transition: all 0.2s ease;
        }

        a.btn:hover {
            background-color: #374151;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="card">
            <div class="card-content space-y-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500 text-white p-3 rounded-lg shadow-md">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <h1 class="text-3xl font-semibold text-blue-600">Log #{{ $log->id }}</h1>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div><span class="font-semibold text-gray-700">URL:</span>
                        <a href="{{ $log->url }}" target="_blank"
                            class="text-blue-600 hover:underline break-words">{{ $log->url }}</a>
                    </div>
                    <div><span class="font-semibold text-gray-700">Method:</span> {{ $log->method }}</div>
                    <div><span class="font-semibold text-gray-700">Status:</span>
                        <span
                            class="px-2 py-1 text-xs rounded-full
              {{ $log->status_code >= 400 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                            {{ $log->status_code ?? 'N/A' }}
                        </span>
                    </div>
                    <div><span class="font-semibold text-gray-700">IP:</span> {{ $log->ip }}</div>
                    <div><span class="font-semibold text-gray-700">Requested:</span>
                        {{ optional($log->requested_at)->format('Y-m-d H:i:s') ?? '-' }}</div>
                    <div><span class="font-semibold text-gray-700">Duration:</span> {{ $log->duration_ms }} ms</div>
                    <div><span class="font-semibold text-gray-700">Action:</span> {{ $log->route_action ?? 'N/A' }}
                    </div>
                    <div><span class="font-semibold text-gray-700">User Agent:</span> <span
                            class="text-gray-600">{{ $log->user_agent }}</span></div>
                </div>

                @if ($log->exception_message)
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                        <div class="font-semibold mb-2"><i class="fas fa-exclamation-triangle mr-2"></i>Exception</div>
                        <p>{{ $log->exception_message }}</p>
                        <pre class="mt-3 text-xs text-red-800">{{ $log->exception_trace }}</pre>
                    </div>
                @endif

                <details open class="group">
                    <summary class="flex items-center space-x-2 text-blue-600 font-semibold text-lg mb-2">
                        <i class="fas fa-code"></i><span>Request Body</span>
                    </summary>
                    <pre>{{ $requestBody }}</pre>
                </details>

                <details open class="group">
                    <summary class="flex items-center space-x-2 text-blue-600 font-semibold text-lg mb-2">
                        <i class="fas fa-server"></i><span>Response Body</span>
                    </summary>
                    <pre>{{ $responseBody }}</pre>
                </details>
            </div>

            <div class="flex justify-end border-t p-4 bg-gray-50">
                <a href="{{ route('scollio-logs.index') }}"
                    class="btn inline-flex items-center px-5 py-2.5 bg-gray-700 text-white rounded-lg shadow-sm hover:bg-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
    </div>
</body>

</html>
