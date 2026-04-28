<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Driver Dashboard — Delivery Queue</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Driver Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Welcome, {{ $driver->name }}</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium
                {{ $driver->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $driver->is_available ? 'Available' : 'Unavailable' }}
            </span>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($deliveries->isEmpty())
            <div class="text-center py-20 bg-white rounded-xl shadow-sm">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
                <p class="mt-4 text-gray-500 font-medium">No deliveries assigned to you yet.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($deliveries as $delivery)
                    @php
                        $statusColors = [
                            'pending'          => 'bg-gray-100 text-gray-700',
                            'picking'          => 'bg-yellow-100 text-yellow-800',
                            'packed'           => 'bg-blue-100 text-blue-800',
                            'shipped'          => 'bg-indigo-100 text-indigo-800',
                            'out_for_delivery' => 'bg-orange-100 text-orange-800',
                            'delivered'        => 'bg-green-100 text-green-800',
                            'undeliverable'    => 'bg-red-100 text-red-800',
                        ];
                        $color = $statusColors[$delivery->status] ?? 'bg-gray-100 text-gray-700';
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                                @if($delivery->tracking_number)
                                    <span class="text-xs text-gray-400 font-mono">{{ $delivery->tracking_number }}</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-700 truncate">
                                <span class="font-medium">To:</span>
                                @if($delivery->address)
                                    {{ $delivery->address->street ?? '' }},
                                    {{ $delivery->address->city ?? '' }}
                                @else
                                    <span class="text-gray-400">Address unavailable</span>
                                @endif
                            </p>
                            @if($delivery->estimated_delivery)
                                <p class="text-xs text-gray-400 mt-1">
                                    Est. {{ $delivery->estimated_delivery->format('d M Y') }}
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('driver.show', ['delivery' => $delivery, 'driver_id' => $driver->id]) }}"
                           class="shrink-0 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            View
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</body>
</html>
