@extends('layouts.app')

@section('content')
    <section class="space-y-8">
        <div class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Address book</p>
                <h1 class="mt-3 text-3xl font-black text-stone-900">Add a delivery address</h1>
                <p class="mt-2 text-sm leading-7 text-stone-600">Keep the validation simple and consistent. Country codes use two letters.</p>

                <form method="POST" action="{{ route('addresses.store') }}" class="mt-8 grid gap-4">
                    @csrf
                    <input name="street" type="text" placeholder="Street" class="rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white" value="{{ old('street') }}" required>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input name="city" type="text" placeholder="City" class="rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white" value="{{ old('city') }}" required>
                        <input name="region" type="text" placeholder="Region" class="rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white" value="{{ old('region') }}">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <input name="country" type="text" placeholder="Country code" maxlength="2" class="rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm uppercase outline-none transition focus:border-amber-500 focus:bg-white" value="{{ old('country') }}" required>
                        <input name="postal_code" type="text" placeholder="Postal code" class="rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white" value="{{ old('postal_code') }}">
                    </div>
                    <label class="inline-flex items-center gap-3 text-sm font-medium text-stone-700">
                        <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                        Make this the default address
                    </label>
                    <button type="submit" class="rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Save address</button>
                </form>
            </div>

            <div class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Saved addresses</p>
                <h2 class="mt-3 text-3xl font-black text-stone-900">Manage what exists</h2>

                <div class="mt-8 space-y-5">
                    @forelse ($addresses as $address)
                        <article class="rounded-[1.5rem] bg-stone-100 p-5">
                            <div class="mb-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-stone-900">{{ $address->street }}</p>
                                    <p class="text-sm text-stone-600">{{ $address->city }}, {{ $address->country }}</p>
                                </div>
                                @if ($address->is_default)
                                    <span class="rounded-full bg-amber-200 px-3 py-1 text-xs font-black uppercase tracking-[0.2em] text-amber-900">Default</span>
                                @endif
                            </div>

                            <form method="POST" action="{{ route('addresses.update', $address) }}" class="grid gap-3">
                                @csrf
                                @method('PUT')
                                <input name="street" type="text" value="{{ $address->street }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500">
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="city" type="text" value="{{ $address->city }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500">
                                    <input name="region" type="text" value="{{ $address->region }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500">
                                </div>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <input name="country" type="text" value="{{ $address->country }}" maxlength="2" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm uppercase outline-none transition focus:border-amber-500">
                                    <input name="postal_code" type="text" value="{{ $address->postal_code }}" class="rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-amber-500">
                                </div>
                                <label class="inline-flex items-center gap-3 text-sm font-medium text-stone-700">
                                    <input type="checkbox" name="is_default" value="1" class="h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500" @checked($address->is_default)>
                                    Default address
                                </label>
                                <button type="submit" class="rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Update</button>
                            </form>
                            <div class="mt-3 flex flex-wrap gap-3">
                                <form method="POST" action="{{ route('addresses.destroy', $address) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-2xl border border-rose-300 px-4 py-3 text-sm font-semibold text-rose-700 transition hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <p class="rounded-2xl bg-stone-100 px-4 py-4 text-sm text-stone-600">No addresses saved yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
