@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md rounded-[2rem] border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Welcome back</p>
        <h1 class="mt-3 text-3xl font-black text-stone-900">Sign in</h1>
        <p class="mt-2 text-sm text-stone-600">Use the seeded account `test@example.com` with password `password`, or create your own.</p>

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
            @csrf
            <div class="space-y-2">
                <label for="email" class="text-sm font-semibold text-stone-800">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white" required>
            </div>
            <div class="space-y-2">
                <label for="password" class="text-sm font-semibold text-stone-800">Password</label>
                <input id="password" name="password" type="password" class="w-full rounded-2xl border border-stone-300 bg-stone-50 px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-amber-500 focus:bg-white" required>
            </div>
            <button type="submit" class="w-full rounded-2xl bg-stone-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-stone-700">Sign in</button>
        </form>
    </div>
@endsection
