<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Delivery #{{ $delivery->tracking_number ?? substr($delivery->id, 0, 8) }} — Driver View</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-10 px-4 sm:px-6">

        <a href="{{ route('driver.index', ['driver_id' => request('driver_id')]) }}"
           class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-6 transition">
            &larr; Back to Queue
        </a>

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Status Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h1 class="text-lg font-bold text-gray-900 mb-4">Delivery Details</h1>

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
            @endphp

            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-gray-500">Status</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                            {{ $statusColors[$delivery->status] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Tracking</dt>
                    <dd class="mt-1 font-mono text-gray-800">{{ $delivery->tracking_number ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Estimated Delivery</dt>
                    <dd class="mt-1 text-gray-800">{{ $delivery->estimated_delivery?->format('d M Y') ?? 'TBD' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Stops Remaining</dt>
                    <dd class="mt-1 text-gray-800">{{ $delivery->stops_remaining ?? '—' }}</dd>
                </div>
                <div class="col-span-2">
                    <dt class="text-gray-500">Delivery Address</dt>
                    <dd class="mt-1 text-gray-800">
                        @if($delivery->address)
                            {{ $delivery->address->street ?? '' }},
                            {{ $delivery->address->city ?? '' }}
                            {{ $delivery->address->postal_code ?? '' }}
                        @else
                            <span class="text-gray-400">Not available</span>
                        @endif
                    </dd>
                </div>
                @if($delivery->delivery_instructions)
                    <div class="col-span-2">
                        <dt class="text-gray-500">Delivery Instructions</dt>
                        <dd class="mt-1 text-gray-800 bg-amber-50 rounded-lg p-3 border border-amber-100">
                            {{ $delivery->delivery_instructions }}
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Status Update Form --}}
        @php
            $nextStatuses = \App\Models\Delivery::STATUS_TRANSITIONS[$delivery->status] ?? [];
        @endphp

        @if(count($nextStatuses) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-base font-semibold text-gray-900 mb-4">Update Status</h2>
                <form method="POST" action="{{ route('driver.update', $delivery) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="driver_id" value="{{ request('driver_id') }}" />

                    <div class="flex items-center gap-3">
                        <select id="status" name="status"
                            class="flex-1 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($nextStatuses as $next)
                                <option value="{{ $next }}">{{ ucfirst(str_replace('_', ' ', $next)) }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="inline-flex items-center px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            Confirm
                        </button>
                    </div>

                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        @else
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-500 text-center">
                This delivery has reached its final status.
            </div>
        @endif

    </div>
</body>
</html>
