<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin — Delivery #{{ $delivery->tracking_number ?? substr($delivery->id, 0, 8) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen">

    <header class="bg-gray-900 border-b border-gray-800 px-6 py-4 flex items-center gap-4">
        <a href="{{ route('admin.deliveries.index') }}" class="text-indigo-400 hover:text-indigo-300 text-sm transition">
            ← All Deliveries
        </a>
        <span class="text-gray-600">/</span>
        <span class="text-gray-300 text-sm font-mono">{{ $delivery->tracking_number ?? substr($delivery->id, 0, 12) }}</span>
    </header>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(session('success'))
            <div class="rounded-lg bg-green-900/50 border border-green-700 px-4 py-3 text-green-300 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Status + Force Update --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <div class="flex items-start justify-between gap-6 flex-wrap">
                <div>
                    <h1 class="text-lg font-bold text-white mb-3">Delivery Status</h1>
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
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        {{ $badgeColors[$delivery->status] ?? 'bg-gray-700 text-gray-300' }}">
                        {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                    </span>
                    <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-gray-500">Tracking</dt>
                            <dd class="font-mono text-gray-300">{{ $delivery->tracking_number ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Driver</dt>
                            <dd class="text-gray-300">{{ $delivery->driver?->name ?? 'Unassigned' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Est. Delivery</dt>
                            <dd class="text-gray-300">{{ $delivery->estimated_delivery?->format('d M Y') ?? 'TBD' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Eco Dispatch</dt>
                            <dd class="text-gray-300">{{ $delivery->eco_dispatch ? '🌿 Yes' : 'No' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Stops Remaining</dt>
                            <dd class="text-gray-300">{{ $delivery->stops_remaining ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Address</dt>
                            <dd class="text-gray-300">
                                {{ $delivery->address?->street ?? '—' }},
                                {{ $delivery->address?->city ?? '' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                {{-- Admin Force Status Update --}}
                <form method="POST" action="{{ route('admin.deliveries.update', $delivery) }}" class="shrink-0 w-64">
                    @csrf
                    @method('PATCH')
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Force Status Update</p>
                    <select name="status" id="admin-status"
                        class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-200 px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach(\App\Models\Delivery::ALL_STATUSES as $s)
                            <option value="{{ $s }}" {{ $delivery->status === $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                    <textarea name="notes" rows="2" placeholder="Admin note (optional)..."
                        class="w-full rounded-lg bg-gray-800 border border-gray-700 text-gray-200 px-3 py-2 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    <button type="submit"
                        class="w-full py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition">
                        Apply Override
                    </button>
                </form>
            </div>
        </div>

        {{-- Claims --}}
        @if($delivery->claims->isNotEmpty())
            <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
                <h2 class="text-base font-semibold text-white mb-4">Claims ({{ $delivery->claims->count() }})</h2>
                <div class="space-y-4">
                    @foreach($delivery->claims as $claim)
                        <div class="bg-gray-800 rounded-lg p-4 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-gray-200">
                                        {{ $claim->type === 'damaged' ? '📦 Damaged' : '🔍 Missing Box' }}
                                    </span>
                                    @php
                                        $claimColors = [
                                            'open'         => 'bg-yellow-800 text-yellow-200',
                                            'under_review' => 'bg-blue-800 text-blue-200',
                                            'resolved'     => 'bg-green-800 text-green-200',
                                            'rejected'     => 'bg-red-800 text-red-200',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ $claimColors[$claim->status] ?? 'bg-gray-700 text-gray-300' }}">
                                        {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 line-clamp-2">{{ $claim->description }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $claim->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                            <form method="POST" action="{{ route('admin.claims.update', $claim) }}" class="shrink-0 flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status"
                                    class="rounded-lg bg-gray-700 border border-gray-600 text-gray-200 px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    @foreach(['open', 'under_review', 'resolved', 'rejected'] as $cs)
                                        <option value="{{ $cs }}" {{ $claim->status === $cs ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $cs)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="px-3 py-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs rounded-lg transition">
                                    Update
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Inventory / Audit Log --}}
        <div class="bg-gray-900 rounded-xl border border-gray-800 p-6">
            <h2 class="text-base font-semibold text-white mb-4">Audit Log</h2>
            @if($delivery->inventoryLogs->isEmpty())
                <p class="text-sm text-gray-500">No events recorded yet.</p>
            @else
                <ol class="relative border-l border-gray-700 ml-3 space-y-5">
                    @foreach($delivery->inventoryLogs as $log)
                        <li class="ml-5">
                            <div class="absolute -left-1.5 w-3 h-3 rounded-full bg-indigo-500 border-2 border-gray-900"></div>
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm text-gray-200 font-medium">
                                        @if($log->event === 'status_changed')
                                            Status changed:
                                            <span class="text-gray-400">{{ ucfirst(str_replace('_', ' ', $log->from_value ?? '?')) }}</span>
                                            → <span class="text-indigo-400">{{ ucfirst(str_replace('_', ' ', $log->to_value ?? '?')) }}</span>
                                        @elseif($log->event === 'claim_filed')
                                            Claim filed: <span class="text-red-400">{{ ucfirst($log->to_value) }}</span>
                                        @else
                                            {{ ucfirst(str_replace('_', ' ', $log->event)) }}
                                        @endif
                                    </p>
                                    @if($log->notes)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $log->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-600 mt-0.5">
                                        By: {{ ucfirst($log->changed_by_type ?? 'system') }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-600 shrink-0">
                                    {{ $log->created_at->format('d M, h:i A') }}
                                </span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>

    </div>
</body>
</html>
