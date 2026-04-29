<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Subscription Box') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('AppIcon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('AppIcon.png') }}">
    <meta name="theme-color" content="#ff385c">
    <script src="https://unpkg.com/lucide@0.511.0/dist/umd/lucide.min.js" defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen">
    <div class="min-h-screen">
        <header class="sticky top-0 z-50 border-b border-hairline/90 bg-canvas/95 backdrop-blur">
            <div class="air-shell flex flex-col gap-4 py-4">
                <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                        <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box icon" class="h-12 w-12 rounded-2xl object-cover shadow-sm ring-1 ring-hairline">
                        <div class="min-w-0">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.32em] text-rausch">Subscription Platform</p>
                            <p class="truncate text-sm font-semibold text-ink">Subscription Box Platform</p>
                        </div>
                    </a>

                    <div class="hidden items-center justify-center gap-8 lg:flex">
                        <a href="{{ route('subscriptions.index') }}" class="group flex flex-col items-center gap-2 text-sm font-medium text-ink transition {{ request()->routeIs('subscriptions.*') ? '' : 'opacity-70 hover:opacity-100' }}">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-cloud text-lg"><i data-lucide="badge-check" class="h-5 w-5"></i></span>
                            <span class="border-b-2 pb-1 {{ request()->routeIs('subscriptions.*') ? 'border-ink' : 'border-transparent group-hover:border-hairline' }}">Subscriptions</span>
                        </a>
                        <a href="{{ route('boxes.index') }}" class="group flex flex-col items-center gap-2 text-sm font-medium text-ink transition {{ request()->routeIs('boxes.*') ? '' : 'opacity-70 hover:opacity-100' }}">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-cloud text-lg"><i data-lucide="package-open" class="h-5 w-5"></i></span>
                            <span class="border-b-2 pb-1 {{ request()->routeIs('boxes.*') ? 'border-ink' : 'border-transparent group-hover:border-hairline' }}">Boxes</span>
                        </a>
                        <a href="{{ route('deliveries.index') }}" class="group flex flex-col items-center gap-2 text-sm font-medium text-ink transition {{ request()->routeIs('deliveries.*') ? '' : 'opacity-70 hover:opacity-100' }}">
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-cloud text-lg"><i data-lucide="truck" class="h-5 w-5"></i></span>
                            <span class="border-b-2 pb-1 {{ request()->routeIs('deliveries.*') ? 'border-ink' : 'border-transparent group-hover:border-hairline' }}">Deliveries</span>
                        </a>
                    </div>

                    <nav class="flex flex-wrap items-center justify-end gap-2 text-sm font-medium text-ash">
                        @auth
                            <span class="hidden items-center gap-2 rounded-full border border-hairline bg-canvas px-4 py-2 text-ink md:inline-flex"><i data-lucide="user-round" class="h-4 w-4"></i>{{ auth()->user()->name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full border border-hairline bg-canvas px-4 py-2 text-sm font-semibold text-ink transition hover:border-ink/30 hover:bg-cloud"><i data-lucide="log-out" class="h-4 w-4"></i>Logout</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-full border border-transparent px-4 py-2 text-sm font-semibold text-ink transition hover:bg-cloud"><i data-lucide="log-in" class="h-4 w-4"></i>Login</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-full bg-rausch px-4 py-2 text-sm font-semibold text-white transition hover:bg-rausch-deep"><i data-lucide="user-plus" class="h-4 w-4"></i>Create account</a>
                        @endauth
                    </nav>
                </div>

                @auth
                    <div class="overflow-x-auto">
                        <div class="flex min-w-max items-center gap-2 pb-1">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Dashboard</a>
                            <a href="{{ route('plans.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('plans.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Plans</a>
                            <a href="{{ route('addresses.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('addresses.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Addresses</a>
                            <a href="{{ route('payments.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('payments.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Payments</a>
                            @if (Route::has('audit-logs.index') && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin())
                                <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('audit-logs.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Audit logs</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </header>

        <main class="air-shell py-8 sm:py-10 lg:py-12">
            @if (session('status'))
                <div class="air-float mb-6 rounded-[20px] border border-emerald-200 bg-canvas px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('success'))
                <div class="air-float mb-6 rounded-[20px] border border-emerald-200 bg-canvas px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="air-float mb-6 rounded-[20px] border border-danger/20 bg-canvas px-4 py-3 text-sm font-medium text-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="air-float mb-6 rounded-[20px] border border-danger/20 bg-canvas px-4 py-3 text-sm text-danger">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="mt-10 border-t border-hairline bg-canvas/90">
            <div class="air-shell grid gap-8 py-8 text-sm text-ash md:grid-cols-3">
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Customer Experience</p>
                    <p>Unified account, billing, box management, and delivery tracking in one platform.</p>
                </div>
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Operations</p>
                    <p>Reliable subscription provisioning, fulfillment tracking, and audit visibility for support teams.</p>
                </div>
                <div class="space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Platform Quality</p>
                    <p>Production-ready interface with consistent design, accessible controls, and clear workflows.</p>
                </div>
            </div>
        </footer>
    </div>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
