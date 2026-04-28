<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Claim #{{ substr($claim->id, 0, 8) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="max-w-2xl mx-auto py-10 px-4 sm:px-6">

        <a href="{{ route('deliveries.show', $claim->delivery) }}"
           class="inline-flex items-center gap-1 text-sm text-indigo-600 hover:text-indigo-800 mb-6 transition">
            &larr; Back to Delivery
        </a>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

            {{-- Header --}}
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Claim Details</h1>
                    <p class="text-xs text-gray-400 font-mono mt-1">{{ $claim->id }}</p>
                </div>
                @php
                    $statusColors = [
                        'open'         => 'bg-yellow-100 text-yellow-800',
                        'under_review' => 'bg-blue-100 text-blue-800',
                        'resolved'     => 'bg-green-100 text-green-800',
                        'rejected'     => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ $statusColors[$claim->status] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ ucfirst(str_replace('_', ' ', $claim->status)) }}
                </span>
            </div>

            {{-- Details --}}
            <dl class="space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <dt class="text-gray-500">Claim Type</dt>
                        <dd class="mt-1 font-semibold text-gray-800">
                            {{ $claim->type === 'damaged' ? '📦 Damaged Item' : '🔍 Missing Box' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Submitted</dt>
                        <dd class="mt-1 text-gray-800">{{ $claim->created_at->format('d M Y, h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Delivery Tracking</dt>
                        <dd class="mt-1 font-mono text-gray-800">
                            {{ $claim->delivery?->tracking_number ?? '—' }}
                        </dd>
                    </div>
                </div>

                <div>
                    <dt class="text-gray-500 mb-1">Description</dt>
                    <dd class="text-gray-800 bg-gray-50 rounded-lg p-4 border border-gray-100 leading-relaxed">
                        {{ $claim->description }}
                    </dd>
                </div>

                @if($claim->photo_path)
                    <div>
                        <dt class="text-gray-500 mb-2">Attached Photo</dt>
                        <dd>
                            <img src="{{ Storage::url($claim->photo_path) }}"
                                 alt="Claim photo"
                                 class="max-h-72 w-auto rounded-lg border border-gray-200 object-cover" />
                        </dd>
                    </div>
                @endif
            </dl>

            {{-- Status Timeline --}}
            <div class="mt-8 pt-6 border-t border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Status Progress</h2>
                <ol class="flex items-center gap-0">
                    @php
                        $steps = ['open', 'under_review', 'resolved'];
                        $currentIndex = array_search($claim->status, $steps);
                        $isRejected = $claim->status === 'rejected';
                    @endphp
                    @foreach($steps as $i => $step)
                        @php
                            $reached = $currentIndex !== false && $i <= $currentIndex && ! $isRejected;
                        @endphp
                        <li class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $reached ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                                    {{ $i + 1 }}
                                </div>
                                <span class="text-xs mt-1 text-gray-500">{{ ucfirst(str_replace('_', ' ', $step)) }}</span>
                            </div>
                            @if($i < count($steps) - 1)
                                <div class="flex-1 h-0.5 mx-2 {{ $currentIndex !== false && $i < $currentIndex && ! $isRejected ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                            @endif
                        </li>
                    @endforeach
                    @if($isRejected)
                        <li class="flex items-center ml-2">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold bg-red-500 text-white">✕</div>
                                <span class="text-xs mt-1 text-red-500">Rejected</span>
                            </div>
                        </li>
                    @endif
                </ol>
            </div>

        </div>
    </div>
</body>
</html>
