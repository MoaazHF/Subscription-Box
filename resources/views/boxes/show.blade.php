<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Box Details - {{ DateTime::createFromFormat('!m', $box->period_month)->format('F') }} {{ $box->period_year }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased font-sans">
    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Navigation -->
        <div class="mb-6">
            <a href="{{ route('boxes.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center">
                &larr; Back to My Boxes
            </a>
        </div>

        <!-- Lock Date Banner -->
        <div class="rounded-xl p-4 mb-8 flex items-center justify-between {{ $box->status === 'locked' ? 'bg-red-50 ring-1 ring-red-200' : 'bg-blue-50 ring-1 ring-blue-200' }}">
            <div class="flex items-center">
                <svg class="h-6 w-6 mr-3 {{ $box->status === 'locked' ? 'text-red-600' : 'text-blue-600' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <div>
                    <h3 class="text-sm font-bold {{ $box->status === 'locked' ? 'text-red-900' : 'text-blue-900' }}">
                        {{ $box->status === 'locked' ? 'Box is Locked' : 'Box is Open for Customization' }}
                    </h3>
                    <p class="text-sm {{ $box->status === 'locked' ? 'text-red-700' : 'text-blue-700' }}">
                        Locks on {{ $box->lock_date ? $box->lock_date->format('F j, Y') : 'N/A' }}
                    </p>
                </div>
            </div>
            @if($box->status !== 'locked')
            <a href="{{ route('boxes.customize', $box) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm transition-colors">
                Customize
            </a>
            @endif
        </div>

        <!-- Box Header -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">
                    {{ DateTime::createFromFormat('!m', $box->period_month)->format('F') }} {{ $box->period_year }} Box
                </h1>
                <p class="text-gray-500 mt-1">Theme: {{ $box->theme ?? 'Standard Theme' }}</p>
            </div>
            <div class="mt-4 sm:mt-0 flex flex-col items-end">
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-1 text-sm font-medium text-gray-800">
                    Total Weight: {{ number_format($box->total_weight_g / 1000, 2) }} kg
                </span>
                <span class="mt-2 inline-flex items-center rounded-md bg-indigo-50 px-2.5 py-1 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                    Tier: {{ ucfirst($box->shipping_tier) }}
                </span>
            </div>
        </div>

        <!-- Items Grid -->
        <h2 class="text-lg font-bold text-gray-900 mb-4">Included Items</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($box->items as $item)
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Item Image Placeholder -->
                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                        <svg class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-gray-900">{{ $item->name ?? 'Unknown Item' }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $item->description ?? 'No description available.' }}</p>
                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-900">{{ $item->weight_g ? $item->weight_g . 'g' : '' }}</span>
                            <span class="text-xs font-semibold tracking-wider text-indigo-600 uppercase bg-indigo-50 px-2 py-1 rounded">Item</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 bg-white rounded-xl shadow-sm ring-1 ring-gray-200 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No items yet</h3>
                    <p class="mt-1 text-sm text-gray-500">This box doesn't have any items assigned.</p>
                </div>
            @endforelse
        </div>

    </div>
</body>
</html>
