<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Customize Your Box</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased" 
      x-data="{ 
          swapModalOpen: {{ session('swap_warning') ? 'true' : 'false' }}, 
          warningItemName: '{{ session('swap_warning.new_item_name', '') }}',
          warningRemoveId: '{{ session('swap_warning.remove_box_item_id', '') }}',
          warningNewId: '{{ session('swap_warning.new_item_id', '') }}',
          
          openSwapModal(outItemId) {
              this.swapModalOpen = true;
              this.warningRemoveId = outItemId;
              this.warningNewId = '';
          }
      }">

    <div class="max-w-5xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Alerts -->
        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 p-4 ring-1 ring-red-200">
                <div class="flex">
                    <div class="ml-3 text-sm text-red-800">
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 ring-1 ring-green-200">
                <div class="flex">
                    <div class="ml-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('boxes.show', $box->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm flex items-center">
                &larr; Back to Box
            </a>
        </div>

        @php
            $isLocked = $box->status === 'locked' || ($box->lock_date && $box->lock_date->isPast());
            $hoursUntilLock = $box->lock_date ? now()->diffInHours($box->lock_date, false) : 999;
        @endphp

        <!-- Lock Date Countdown -->
        <div class="rounded-xl p-4 mb-8 flex items-center justify-between {{ $isLocked ? 'bg-red-50 ring-1 ring-red-200' : 'bg-white shadow-sm ring-1 ring-gray-200' }}">
            <div>
                <h3 class="text-lg font-bold {{ $isLocked ? 'text-red-900' : 'text-gray-900' }}">
                    {{ $isLocked ? 'Customization Locked' : 'Customization Open' }}
                </h3>
                @if(!$isLocked)
                    <p class="text-sm mt-1 {{ $hoursUntilLock < 48 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                        Locks in {{ $box->lock_date->diffForHumans() }} ({{ $box->lock_date->format('M d, Y g:i A') }})
                    </p>
                @endif
            </div>
        </div>

        <!-- Weight Progress & Tier -->
        <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 p-6 mb-8">
            <div class="flex justify-between items-end mb-2">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide">Box Weight</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($box->total_weight_g / 1000, 2) }} <span class="text-lg text-gray-500">/ 3.00 kg</span></p>
                </div>
                <span class="inline-flex items-center rounded-md bg-indigo-50 px-3 py-1.5 text-sm font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">
                    Tier: {{ ucfirst($box->shipping_tier) }}
                </span>
            </div>
            @php
                $weightPercent = min(100, ($box->total_weight_g / 3000) * 100);
                $barColor = $weightPercent > 90 ? 'bg-red-500' : ($weightPercent > 70 ? 'bg-yellow-400' : 'bg-green-500');
            @endphp
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-500" style="width: {{ $weightPercent }}%"></div>
            </div>
        </div>

        <!-- Items Grid -->
        <h2 class="text-xl font-extrabold text-gray-900 mb-6">Current Items</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($box->items as $item)
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-200 overflow-hidden flex flex-col">
                    <div class="h-40 bg-gray-100 flex items-center justify-center">
                        <span class="text-gray-400 font-medium text-sm">Image Placeholder</span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-lg font-bold text-gray-900">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1 flex-1">{{ $item->weight_g }}g</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 flex gap-3">
                            <button 
                                @click="openSwapModal('{{ $item->pivot->id }}')" 
                                class="flex-1 justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                {{ $isLocked ? 'disabled' : '' }}>
                                Swap
                            </button>
                            <form action="{{ route('boxes.remove', ['box' => $box->id, 'boxItem' => $item->pivot->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex-1 justify-center rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-200 hover:bg-red-100 disabled:opacity-50 disabled:cursor-not-allowed" {{ $isLocked ? 'disabled' : '' }}>
                                    Remove
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <!-- Swap Modal -->
    <div x-show="swapModalOpen" class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" x-show="swapModalOpen" x-transition.opacity></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                     x-show="swapModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="swapModalOpen = false">
                    
                    <div>
                        <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Swap Item</h3>
                        
                        <!-- Warning State inside Modal -->
                        @if (session('swap_warning'))
                            <div class="mt-4 rounded-md bg-orange-50 p-4 ring-1 ring-orange-200">
                                <div class="flex">
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-orange-800">Warning</h3>
                                        <div class="mt-2 text-sm text-orange-700">
                                            <p>{{ session('swap_warning.message') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <form action="{{ route('boxes.swap', $box->id) }}" method="POST" class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                @csrf
                                <input type="hidden" name="remove_box_item_id" value="{{ session('swap_warning.remove_box_item_id') }}">
                                <input type="hidden" name="new_item_id" value="{{ session('swap_warning.new_item_id') }}">
                                
                                @if(session('swap_warning.type') === 'rotation')
                                    <input type="hidden" name="confirm_rotation" value="1">
                                @else
                                    <input type="hidden" name="confirm_allergen" value="1">
                                @endif
                                
                                <button type="submit" class="inline-flex w-full justify-center rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:col-start-2">Confirm Swap Anyway</button>
                                <button type="button" @click="swapModalOpen = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">Cancel</button>
                            </form>
                        @else
                            <form action="{{ route('boxes.swap', $box->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="remove_box_item_id" x-model="warningRemoveId">
                                <div class="mt-4">
                                    <label for="new_item_id" class="block text-sm font-medium leading-6 text-gray-900">Select new item</label>
                                    <select id="new_item_id" name="new_item_id" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        @foreach ($availableItems as $ai)
                                            <option value="{{ $ai->id }}">{{ $ai->name }} ({{ $ai->weight_g }}g)</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:col-start-2">Swap Item</button>
                                    <button type="button" @click="swapModalOpen = false" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">Cancel</button>
                                </div>
                            </form>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
