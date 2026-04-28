<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Delivery Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Status Information</h3>
                    <div class="border-t border-gray-200 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Current Status</p>
                            <p class="mt-1">
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                    @if($delivery->status === 'delivered') bg-green-100 text-green-800 
                                    @elseif(in_array($delivery->status, ['shipped', 'out_for_delivery'])) bg-blue-100 text-blue-800 
                                    @elseif($delivery->status === 'undeliverable') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $delivery->status)) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tracking Number</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $delivery->tracking_number ?? 'Not assigned yet' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Estimated Delivery Date</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $delivery->estimated_delivery ? $delivery->estimated_delivery->format('F d, Y') : 'TBD' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Actual Delivery Date</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $delivery->actual_delivery ? $delivery->actual_delivery->format('F d, Y h:i A') : '--' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Eco-Friendly Dispatch</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $delivery->eco_dispatch ? 'Yes' : 'No' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Delivery Address</h3>
                        <div class="border-t border-gray-200 py-4">
                            @if($delivery->address)
                                <address class="not-italic text-sm text-gray-600">
                                    {{ $delivery->address->street }}<br>
                                    {{ $delivery->address->city }}, {{ $delivery->address->region ?? '' }} {{ $delivery->address->postal_code }}<br>
                                    {{ $delivery->address->country }}
                                </address>
                            @else
                                <p class="text-sm text-gray-500">Address information not available.</p>
                            @endif
                        </div>

                        <h4 class="text-md font-medium text-gray-900 mt-4 mb-2">Delivery Instructions</h4>
                        <p class="text-sm text-gray-600 border-t border-gray-200 pt-2">
                            {{ $delivery->delivery_instructions ?? 'None provided.' }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Box Information</h3>
                        <div class="border-t border-gray-200 py-4">
                            @if($delivery->box)
                                <p class="text-sm text-gray-600"><span class="font-medium text-gray-900">Box ID:</span> {{ $delivery->box->id }}</p>
                                <p class="text-sm text-gray-600 mt-2"><span class="font-medium text-gray-900">Theme:</span> {{ $delivery->box->theme ?? 'Standard' }}</p>
                                <p class="text-sm text-gray-600 mt-2"><span class="font-medium text-gray-900">Period:</span> {{ $delivery->box->period_month }}/{{ $delivery->box->period_year }}</p>
                            @else
                                <p class="text-sm text-gray-500">Box details not available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Claims Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Have an issue with this delivery?</h3>
                        <p class="text-sm text-gray-500 mt-1">Report missing items or damages using our claims system.</p>
                    </div>
                    <div>
                        <!-- Placeholder claims button - Route to be implemented in another scope -->
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="alert('Claims module not yet implemented!')">
                            File a Claim
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
