@php($title = 'Addresses')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel">
                <p class="air-kicker">Address book</p>
                <h1 class="air-title">Add and validate subscriber destinations.</h1>
                <p class="air-copy">Select a location directly on the map and save the generated address data for downstream delivery flow.</p>

                <form method="POST" action="{{ route('addresses.store') }}" class="mt-8 grid gap-4">
                    @csrf

                    <div class="space-y-3">
                        <label for="address-search" class="text-sm font-semibold text-ink">Find location</label>
                        <div class="flex flex-col gap-3 sm:flex-row">
                            <input id="address-search" type="text" placeholder="Search by area, city, or street" class="air-input" autocomplete="off">
                            <button type="button" id="address-search-button" class="air-button-secondary min-w-[110px] cursor-pointer">Search</button>
                        </div>
                        <div id="address-map" class="h-72 w-full overflow-hidden rounded-[20px] border border-hairline"></div>
                        <p class="text-xs text-ash">Click on map or drag marker. Address fields will be populated automatically.</p>
                    </div>

                    <div class="rounded-[26px] border border-hairline bg-cloud p-3">
                        <div class="grid gap-3 md:grid-cols-[1.2fr_0.9fr_0.9fr_auto] md:items-end">
                            <div class="rounded-[22px] bg-canvas px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Street</p>
                                <input id="street" name="street" type="text" placeholder="Pick on map" class="mt-2 w-full border-0 bg-transparent p-0 text-sm font-semibold text-ink outline-none placeholder:text-ash" value="{{ old('street') }}" required readonly>
                            </div>
                            <div class="rounded-[22px] bg-canvas px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">City</p>
                                <input id="city" name="city" type="text" placeholder="Pick on map" class="mt-2 w-full border-0 bg-transparent p-0 text-sm font-semibold text-ink outline-none placeholder:text-ash" value="{{ old('city') }}" required readonly>
                            </div>
                            <div class="rounded-[22px] bg-canvas px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Country</p>
                                <input id="country" name="country" type="text" placeholder="US" maxlength="2" class="mt-2 w-full border-0 bg-transparent p-0 text-sm font-semibold uppercase text-ink outline-none placeholder:text-ash" value="{{ old('country') }}" required readonly>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="flex h-12 w-12 items-center justify-center rounded-full bg-rausch text-lg text-white">→</button>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="region" class="text-sm font-semibold text-ink">Region</label>
                            <input id="region" name="region" type="text" placeholder="State / region from map" class="air-input" value="{{ old('region') }}" readonly>
                        </div>
                        <div class="space-y-2">
                            <label for="postal_code" class="text-sm font-semibold text-ink">Postal code</label>
                            <input id="postal_code" name="postal_code" type="text" placeholder="Postal code from map" class="air-input" value="{{ old('postal_code') }}" readonly>
                        </div>
                    </div>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                        <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch">
                        Make this the default address
                    </label>
                </form>
            </div>

            <div class="air-panel">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="air-kicker">Saved addresses</p>
                        <h2 class="air-title">Keep each destination editable.</h2>
                    </div>
                    <span class="air-chip-dark">{{ $addresses->count() }} saved</span>
                </div>

                <div class="mt-8 space-y-4">
                    @forelse ($addresses as $address)
                        <article class="air-grid-card space-y-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-lg font-semibold tracking-[-0.01em] text-ink">{{ $address->street }}</p>
                                    <p class="mt-2 text-sm text-ash">{{ $address->city }}, {{ $address->country }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @if ($address->is_default)
                                        <span class="air-chip-dark">Default</span>
                                    @endif
                                    <span class="air-chip">{{ $address->postal_code ?: 'No postal code' }}</span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('addresses.update', $address) }}" class="grid gap-3">
                                @csrf
                                @method('PUT')

                                <input name="street" type="text" value="{{ $address->street }}" class="air-input">

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="city" type="text" value="{{ $address->city }}" class="air-input">
                                    <input name="region" type="text" value="{{ $address->region }}" class="air-input">
                                </div>

                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="country" type="text" value="{{ $address->country }}" maxlength="2" class="air-input uppercase">
                                    <input name="postal_code" type="text" value="{{ $address->postal_code }}" class="air-input">
                                </div>

                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <label class="inline-flex items-center gap-3 text-sm font-medium text-ink">
                                        <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-hairline text-rausch focus:ring-rausch" @checked($address->is_default)>
                                        Default address
                                    </label>

                                    <div class="flex flex-wrap gap-3">
                                        <button type="submit" class="air-button-primary">Update</button>
                                    </div>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="air-button-danger">Delete</button>
                            </form>
                        </article>
                    @empty
                        <div class="air-panel-soft">
                            <p class="text-sm text-ash">No addresses saved yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>
    </section>
@endsection

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            const mapElement = document.getElementById('address-map');
            const streetField = document.getElementById('street');
            const cityField = document.getElementById('city');
            const regionField = document.getElementById('region');
            const countryField = document.getElementById('country');
            const postalCodeField = document.getElementById('postal_code');
            const latitudeField = document.getElementById('latitude');
            const longitudeField = document.getElementById('longitude');
            const searchField = document.getElementById('address-search');
            const searchButton = document.getElementById('address-search-button');

            if (!mapElement || typeof L === 'undefined') {
                return;
            }

            const map = L.map(mapElement).setView([30.0444, 31.2357], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            const marker = L.marker([30.0444, 31.2357], { draggable: true }).addTo(map);

            const setFieldsFromAddress = function (address, lat, lon) {
                const houseNumber = address.house_number || '';
                const road = address.road || address.pedestrian || address.footway || address.cycleway || '';
                const streetValue = [houseNumber, road].filter(Boolean).join(' ').trim();
                streetField.value = streetValue || address.neighbourhood || address.suburb || address.display_name || '';
                cityField.value = address.city || address.town || address.village || address.county || '';
                regionField.value = address.state || address.region || '';
                countryField.value = (address.country_code || '').toUpperCase();
                postalCodeField.value = address.postcode || '';
                latitudeField.value = lat.toFixed(7);
                longitudeField.value = lon.toFixed(7);
            };

            const reverseGeocode = function (lat, lon) {
                fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Reverse geocoding failed');
                        }

                        return response.json();
                    })
                    .then(function (payload) {
                        if (!payload.address) {
                            return;
                        }

                        setFieldsFromAddress(payload.address, lat, lon);
                    })
                    .catch(function () {
                        latitudeField.value = lat.toFixed(7);
                        longitudeField.value = lon.toFixed(7);
                    });
            };

            const moveTo = function (lat, lon, zoom = 16) {
                marker.setLatLng([lat, lon]);
                map.setView([lat, lon], zoom);
                reverseGeocode(lat, lon);
            };

            map.on('click', function (event) {
                moveTo(event.latlng.lat, event.latlng.lng);
            });

            marker.on('dragend', function () {
                const point = marker.getLatLng();
                moveTo(point.lat, point.lng, map.getZoom());
            });

            const searchLocation = function () {
                const query = searchField.value.trim();

                if (!query) {
                    return;
                }

                fetch(`https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1`)
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Search failed');
                        }

                        return response.json();
                    })
                    .then(function (results) {
                        if (!Array.isArray(results) || results.length === 0) {
                            return;
                        }

                        const result = results[0];
                        moveTo(parseFloat(result.lat), parseFloat(result.lon));
                    })
                    .catch(function () {
                        return;
                    });
            };

            searchButton.addEventListener('click', searchLocation);
            searchField.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    searchLocation();
                }
            });

            const hasOldCoordinates = latitudeField.value && longitudeField.value;

            if (hasOldCoordinates) {
                moveTo(parseFloat(latitudeField.value), parseFloat(longitudeField.value));
                return;
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    moveTo(position.coords.latitude, position.coords.longitude);
                }, function () {
                    reverseGeocode(30.0444, 31.2357);
                });
            } else {
                reverseGeocode(30.0444, 31.2357);
            }
        });
    </script>
@endpush
