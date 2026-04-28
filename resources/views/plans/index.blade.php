@extends('layouts.app')

@section('content')
    <section>
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Subscription plans</p>
        <h1 class="mt-3 text-3xl font-black text-stone-900">Choose the tier that fits the box</h1>
        <p class="mt-2 max-w-2xl text-sm leading-7 text-stone-600">This page is intentionally simple. Each plan exposes price, item cap, and total weight so subscribers can compare without guesswork.</p>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">
            @foreach ($plans as $plan)
                <article class="rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">{{ $plan->name }}</p>
                            <h2 class="mt-2 text-4xl font-black text-stone-900">${{ number_format((float) $plan->price_monthly, 2) }}</h2>
                            <p class="text-sm text-stone-500">monthly</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-black uppercase tracking-[0.2em] text-emerald-800">Active</span>
                    </div>

                    <dl class="mt-8 space-y-4 text-sm text-stone-600">
                        <div class="flex items-center justify-between">
                            <dt>Max items</dt>
                            <dd class="font-semibold text-stone-900">{{ $plan->max_items }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt>Weight limit</dt>
                            <dd class="font-semibold text-stone-900">{{ number_format($plan->max_weight_g) }} g</dd>
                        </div>
                        @foreach (($plan->features ?? []) as $feature => $value)
                            <div class="flex items-center justify-between gap-4">
                                <dt>{{ ucwords(str_replace('_', ' ', $feature)) }}</dt>
                                <dd class="font-semibold text-stone-900">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </article>
            @endforeach
        </div>
    </section>
@endsection
