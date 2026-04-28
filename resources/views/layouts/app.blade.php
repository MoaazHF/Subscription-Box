<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Subscription Box') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 text-stone-900 antialiased">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(217,119,6,0.12),_transparent_36%),linear-gradient(180deg,_#fafaf9,_#f5f5f4)]">
        <header class="border-b border-stone-200/80 bg-white/80 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-600 text-sm font-black uppercase tracking-[0.2em] text-white">SB</span>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-amber-700">Team 1 Core</p>
                        <p class="text-sm font-semibold text-stone-900">Subscription Box Platform</p>
                    </div>
                </a>

                <nav class="flex flex-wrap items-center gap-2 text-sm font-medium text-stone-600">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('dashboard') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Dashboard</a>
                        <a href="{{ route('plans.index') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('plans.*') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Plans</a>
                        <a href="{{ route('subscriptions.index') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('subscriptions.*') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Subscriptions</a>
                        <a href="{{ route('addresses.index') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('addresses.*') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Addresses</a>
                        <a href="{{ route('payments.index') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('payments.*') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Payments</a>
                        <a href="{{ route('boxes.index') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('boxes.*') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Boxes</a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('audit-logs.index') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70 {{ request()->routeIs('audit-logs.*') ? 'bg-stone-900 text-white hover:bg-stone-900' : '' }}">Audit Logs</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-amber-600 px-4 py-2 text-white transition hover:bg-amber-700">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-3 py-2 hover:bg-stone-200/70">Login</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-stone-900 px-4 py-2 text-white transition hover:bg-stone-700">Create Account</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
