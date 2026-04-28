@extends('layouts.app')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[1.05fr_0.95fr] xl:items-center">
        <div class="air-float overflow-hidden rounded-[32px] border border-hairline bg-canvas">
            <div class="aspect-[4/3] bg-[linear-gradient(155deg,#fff2f5_0%,#ffffff_52%,#f7f7f7_100%)] p-8">
                <div class="flex h-full flex-col justify-between">
                    <div class="space-y-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Welcome back</p>
                        <h1 class="max-w-xl text-5xl font-bold tracking-[-0.05em] text-ink">Sign in and continue the full subscriber journey.</h1>
                        <p class="max-w-lg text-sm leading-7 text-ash">Use the seeded account `test@example.com` with password `password`, or use your own account to create a box and delivery flow.</p>
                    </div>
                    <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box app icon" class="h-24 w-24 rounded-[28px] object-cover ring-1 ring-white/80 shadow-[0_24px_60px_rgba(255,56,92,0.18)]">
                </div>
            </div>
        </div>

        <div class="air-float rounded-[32px] border border-hairline bg-canvas p-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Account access</p>
            <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Sign in</h2>

            <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
                @csrf
                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-ink">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10" required>
                </div>
                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-ink">Password</label>
                    <input id="password" name="password" type="password" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10" required>
                </div>
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-rausch px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-rausch-deep">Sign in</button>
            </form>
        </div>
    </section>
@endsection
