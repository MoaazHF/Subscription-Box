<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin — Delivery Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">

    {{-- Topbar --}}
    <header class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                </svg>
            </div>
            <span class="font-semibold text-lg text-white">Delivery Admin</span>
        </div>
        <span class="text-sm text-gray-400">All Deliveries</span>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-900/50 border border-green-700 px-4 py-3 text-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Status Stat Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-8">
            @php
                $statusColors = [
                    'pending'          => 'bg-gray-800 border-gray-700 text-gray-300',
                    'picking'          => 'bg-yellow-900/40 border-yellow-700 text-yellow-300',
                    'packed'           => 'bg-blue-900/40 border-blue-700 text-blue-300',
                    'shipped'          => 'bg-indigo-900/40 border-indigo-700 text-indigo-300',
                    'out_for_delivery' => 'bg-orange-900/40 border-orange-700 text-orange-300',
                    'delivered'        => 'bg-green-900/40 border-green-700 text-green-300',
                    'undeliverable'    => 'bg-red-900/40 border-red-700 text-red-300',
                ];
            @endphp
            @foreach($statusCounts as $s => $count)
                <a href="{{ route('admin.deliveries.index', ['status' => $s]) }}"
                   class="rounded-xl border px-3 py-3 text-center transition hover:scale-105 {{ $statusColors[$s] }}
                          {{ $status === $s ? 'ring-2 ring-white/30' : '' }}">
                    <div class="text-2xl font-bold">{{ $count }}</div>
                    <div class="text-xs mt-1 opacity-80">{{ ucfirst(str_replace('_', ' ', $s)) }}</div>
                </a>
            @endforeach
        </div>

        {{-- Filter Bar --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.deliveries.index') }}"
               class="px-3 py-1.5 rounded-lg text-sm {{ ! $status ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' }} transition">
                All
            </a>
            @foreach(\App\Models\Delivery::ALL_STATUSES as $s)
                <a href="{{ route('admin.deliveries.index', ['status' => $s]) }}"
                   class="px-3 py-1.5 rounded-lg text-sm {{ $status === $s ? 'bg-indigo-600 text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700' }} transition">
                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                </a>
            @endforeach
        </div>

        {{-- Deliveries Table --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-800 text-gray-400 text-left">
                        <th class="px-5 py-3 font-medium">Tracking</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Address</th>
                        <th class="px-5 py-3 font-medium">Driver</th>
                        <th class="px-5 py-3 font-medium">Claims</th>
                        <th class="px-5 py-3 font-medium">Est. Delivery</th>
                        <th class="px-5 py-3 font-medium"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($deliveries as $delivery)
                        @php
                            $badgeColors = [
                                'pending'          => 'bg-gray-700 text-gray-300',
                                'picking'          => 'bg-yellow-800 text-yellow-200',
                                'packed'           => 'bg-blue-800 text-blue-200',
                                'shipped'          => 'bg-indigo-800 text-indigo-200',
                                'out_for_delivery' => 'bg-orange-800 text-orange-200',
                                'delivered'        => 'bg-green-800 text-green-200',
                                'undeliverable'    => 'bg-red-800 text-red-200',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-800/50 transition">
                            <td class="px-5 py-3 font-mono text-gray-300 text-xs">
                                {{ $delivery->tracking_number ?? substr($delivery->id, 0, 12).'…' }}
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                    {{ $badgeColors[$delivery->status] ?? 'bg-gray-700 text-gray-300' }}">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-400">
                                {{ $delivery->address?->city ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-gray-400">
                                {{ $delivery->driver?->name ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($delivery->claims_count > 0)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-700 text-red-200 text-xs font-bold">
                                        {{ $delivery->claims_count }}
                                    </span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-400">
                                {{ $delivery->estimated_delivery?->format('d M Y') ?? 'TBD' }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <a href="{{ route('admin.deliveries.show', $delivery) }}"
                                   class="text-indigo-400 hover:text-indigo-300 text-xs font-medium transition">
                                    Manage →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-500">
                                No deliveries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($deliveries->hasPages())
                <div class="px-5 py-4 border-t border-gray-800">
                    {{ $deliveries->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
</body>
</html>
