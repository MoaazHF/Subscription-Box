@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-xl rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Create account</p>
        <h1 class="mt-3 text-3xl font-black text-stone-900">Start simple</h1>
        <p class="mt-2 text-sm text-stone-600">New accounts are created as subscribers. Admin access stays restricted to seeded admin users.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-8 grid gap-5 sm:grid-cols-2">
            @csrf
            <div class="space-y-2 sm:col-span-2">
                <label for="name" class="text-sm font-semibold text-stone-800">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white" required>
            </div>
            <div class="space-y-2">
                <label for="phone" class="text-sm font-semibold text-stone-800">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white">
            </div>
            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold text-stone-800">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white" required>
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-semibold text-stone-800">Password</label>
                <input id="password" name="password" type="password" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white" required>
            </div>
            <div class="space-y-2">
                <label for="password_confirmation" class="text-sm font-semibold text-stone-800">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white" required>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Create account</button>
            </div>
        </form>
    </div>
@endsection
