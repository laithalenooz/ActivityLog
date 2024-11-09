<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.4/flowbite.min.js"></script>
    <style>
        body::-webkit-scrollbar {
            width: 12px;               /* width of the entire scrollbar */
        }

        body::-webkit-scrollbar-track {
            background: #ffffff;        /* color of the tracking area */
        }

        body::-webkit-scrollbar-thumb {
            background-color: #74747a;    /* color of the scroll thumb */
            border-radius: 20px;       /* roundness of the scroll thumb */
            border: 3px solid #ffffff;  /* creates padding around scroll thumb */
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Activity Logs</h1>

    <!-- Filters -->
    <form method="GET" action="{{ url('/activity-logs') }}" class="mb-8 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Log Name Filter -->
            <div>
                <label for="log_name" class="block text-sm font-medium text-gray-700">Log Name</label>
                <select name="log_name[]" id="log_name" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Log Names</option>
                    @foreach($logNames as $name)
                        <option value="{{ $name }}" {{ request('log_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Causer Name Filter -->
            <div>
                <label for="causer_name" class="block text-sm font-medium text-gray-700">Causer Name</label>
                <input type="text" name="causer_name" id="causer_name" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ request('causer_name') }}" placeholder="Enter causer name">
            </div>

            <!-- Date From Filter -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                <input type="date" name="date_from" id="date_from" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ request('date_from') }}">
            </div>

            <!-- Date To Filter -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                <input type="date" name="date_to" id="date_to" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" value="{{ request('date_to') }}">
            </div>
        </div>

        <!-- Submit and Reset Buttons -->
        <div class="flex items-center space-x-4">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Filter
            </button>
            <a href="{{ url('/activity-logs') }}" class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-md shadow hover:bg-gray-600 focus:ring-2 focus:ring-offset-2 focus:ring-gray-400">
                Reset
            </a>
        </div>
    </form>

    <!-- Logs Table -->
    <div class="overflow-hidden shadow rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Log Name</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Causer</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Properties</th>
                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            @forelse($logs as $log)
                <tr>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->log_name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ Str::limit($log->description, 20) }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->causer['name'] ?? '-' }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $log->subject['name'] ?? class_basename($log->subject['type'] ?? '') }} (ID: {{ $log->subject['id'] ?? '-' }})</td>
                    <td class="px-4 py-2 whitespace-nowrap">
                        @if(!empty($log->properties))
                            <button onclick="openModal('propertiesModal{{ $log->_id }}')" class="text-blue-600 hover:underline">View</button>

                            <!-- Modal -->
                            <div id="propertiesModal{{ $log->_id }}" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
                                <div class="flex items-center justify-center min-h-screen px-4">
                                    <div class="bg-white rounded-lg shadow-xl overflow-hidden max-w-fit">
                                        <div class="px-4 py-2 bg-gray-800 text-white text-lg font-semibold">Properties</div>
                                        <div class="p-4">
                                            <pre class="text-gray-700">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                        <div class="px-4 py-2 bg-gray-100 text-right">
                                            <button onclick="closeModal('propertiesModal{{ $log->_id }}')" class="px-4 py-2 bg-gray-600 text-white rounded-md">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-2 text-center text-gray-500">No logs found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $logs->appends(request()->query())->links() }}
    </div>
</div>

<!-- JavaScript for Modal -->
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>

</body>
</html>
