@extends('layouts.app')

@section('content')
    <section class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr] xl:items-center">
        <div class="air-float overflow-hidden rounded-[32px] border border-hairline bg-canvas">
            <div class="aspect-[4/3] bg-[linear-gradient(155deg,#ffffff_0%,#fff3f6_44%,#f7f7f7_100%)] p-8">
                <div class="flex h-full flex-col justify-between">
                    <div class="space-y-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Create account</p>
                        <h1 class="max-w-xl text-5xl font-bold tracking-[-0.05em] text-ink">Start with the subscription foundation.</h1>
                        <p class="max-w-lg text-sm leading-7 text-ash">New accounts enter as subscribers. From there the app can generate the current box and the first delivery record automatically.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[24px] bg-canvas/90 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Onboarding</p>
                            <p class="mt-2 text-sm font-semibold text-ink">Account and subscription setup</p>
                        </div>
                        <div class="rounded-[24px] bg-canvas/90 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-ash">Chain result</p>
                            <p class="mt-2 text-sm font-semibold text-ink">Box + delivery visibility</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="air-float rounded-[32px] border border-hairline bg-canvas p-8">
            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Subscriber setup</p>
            <h2 class="mt-3 text-[2rem] font-bold tracking-[-0.04em] text-ink">Create account</h2>

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
                @csrf
                <div class="space-y-2 sm:col-span-2">
                    <label for="name" class="text-sm font-semibold text-ink">Full name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10" required>
                </div>
                <div class="space-y-2">
                    <label for="phone" class="text-sm font-semibold text-ink">Phone</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10">
                </div>
                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-ink">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10" required>
                </div>
                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-ink">Password</label>
                    <input id="password" name="password" type="password" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10" required>
                </div>
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-ink">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-2xl border border-hairline bg-canvas px-4 py-3.5 text-sm text-ink outline-none transition focus:border-ink focus:ring-2 focus:ring-ink/10" required>
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-rausch px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-rausch-deep">Create account</button>
                </div>
            </form>
        </div>
    </section>
@endsection
