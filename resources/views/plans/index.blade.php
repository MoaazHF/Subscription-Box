@extends('layouts.app')

@section('content')
    <section>
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Subscription plans</p>
                <h1 class="mt-3 text-5xl font-bold tracking-[-0.05em] text-ink">Choose the tier that fits the box.</h1>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-ash">Each plan exposes monthly pricing, item cap, and weight budget so the rest of the product can calculate the box and delivery flow cleanly.</p>
            </div>
            @auth
                <a href="{{ route('subscriptions.index') }}" class="inline-flex items-center rounded-full bg-rausch px-5 py-3 text-sm font-semibold text-white transition hover:bg-rausch-deep">Start subscription flow</a>
            @endauth
        </div>

        <div class="mt-10 grid gap-6 lg:grid-cols-3">
            @foreach ($plans as $plan)
                <article class="overflow-hidden rounded-[30px] border border-hairline bg-canvas">
                    <div class="aspect-[4/3] p-8 {{ $loop->first ? 'bg-[linear-gradient(160deg,#fff4f7_0%,#ffffff_60%,#f7f7f7_100%)]' : ($loop->iteration === 2 ? 'bg-[linear-gradient(160deg,#fff8f3_0%,#ffffff_58%,#f7f7f7_100%)]' : 'bg-[linear-gradient(160deg,#f7efff_0%,#ffffff_58%,#f7f7f7_100%)]') }}">
                        <div class="flex h-full flex-col justify-between">
                            <div class="flex items-center justify-between gap-4">
                                <span class="inline-flex rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-ink">{{ $plan->name }}</span>
                                <span class="inline-flex rounded-full border border-hairline bg-canvas px-3 py-1 text-xs font-semibold text-ink">Active</span>
                            </div>
                            <div>
                                <h2 class="text-5xl font-bold tracking-[-0.05em] text-ink">${{ number_format((float) $plan->price_monthly, 2) }}</h2>
                                <p class="mt-1 text-sm text-ash">monthly</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5 p-8">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ash">Max items</span>
                            <span class="font-semibold text-ink">{{ $plan->max_items }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-ash">Weight limit</span>
                            <span class="font-semibold text-ink">{{ number_format($plan->max_weight_g) }} g</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-ash">Flow</span>
                            <span class="font-semibold text-ink">Subscription → Box</span>
                        </div>
                        @foreach (($plan->features ?? []) as $feature => $value)
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm text-ash">{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                                <span class="text-sm font-semibold text-ink">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : $value }}</span>
                            </div>
                        @endforeach
                    </div>
                </article>
            @endforeach
        </div>
    </section>
@endsection
