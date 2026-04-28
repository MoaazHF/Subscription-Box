@php($title = 'Addresses')

@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
            <div class="air-panel">
                <p class="air-kicker">Address book</p>
                <h1 class="air-title">Add and validate subscriber destinations.</h1>
                <p class="air-copy">Keep the address model simple, keep the forms readable, and make the default address obvious for every downstream delivery flow.</p>

                <form method="POST" action="{{ route('addresses.store') }}" class="mt-8 grid gap-4">
                    @csrf

                    <div class="rounded-[26px] border border-hairline bg-cloud p-3">
                        <div class="grid gap-3 md:grid-cols-[1.2fr_0.9fr_0.9fr_auto] md:items-end">
                            <div class="rounded-[22px] bg-canvas px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Street</p>
                                <input name="street" type="text" placeholder="Street" class="mt-2 w-full border-0 bg-transparent p-0 text-sm font-semibold text-ink outline-none placeholder:text-ash" value="{{ old('street') }}" required>
                            </div>
                            <div class="rounded-[22px] bg-canvas px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">City</p>
                                <input name="city" type="text" placeholder="City" class="mt-2 w-full border-0 bg-transparent p-0 text-sm font-semibold text-ink outline-none placeholder:text-ash" value="{{ old('city') }}" required>
                            </div>
                            <div class="rounded-[22px] bg-canvas px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-mute">Country</p>
                                <input name="country" type="text" placeholder="EG" maxlength="2" class="mt-2 w-full border-0 bg-transparent p-0 text-sm font-semibold uppercase text-ink outline-none placeholder:text-ash" value="{{ old('country') }}" required>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="flex h-12 w-12 items-center justify-center rounded-full bg-rausch text-lg text-white">→</button>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="region" class="text-sm font-semibold text-ink">Region</label>
                            <input id="region" name="region" type="text" placeholder="Governorate / state" class="air-input" value="{{ old('region') }}">
                        </div>
                        <div class="space-y-2">
                            <label for="postal_code" class="text-sm font-semibold text-ink">Postal code</label>
                            <input id="postal_code" name="postal_code" type="text" placeholder="Postal code" class="air-input" value="{{ old('postal_code') }}">
                        </div>
                    </div>

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
