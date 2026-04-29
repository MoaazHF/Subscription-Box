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
                        @auth
                            @if (auth()->user()->isDriver())
                                <a href="{{ route('driver.index') }}" class="group flex flex-col items-center gap-2 text-sm font-medium text-ink transition {{ request()->routeIs('driver.*') ? '' : 'opacity-70 hover:opacity-100' }}">
                                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-cloud text-lg"><i data-lucide="truck" class="h-5 w-5"></i></span>
                                    <span class="border-b-2 pb-1 {{ request()->routeIs('driver.*') ? 'border-ink' : 'border-transparent group-hover:border-hairline' }}">Driver</span>
                                </a>
                            @else
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
                            @endif
                        @else
                            <a href="{{ route('plans.index') }}" class="group flex flex-col items-center gap-2 text-sm font-medium text-ink transition {{ request()->routeIs('plans.*') ? '' : 'opacity-70 hover:opacity-100' }}">
                                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-cloud text-lg"><i data-lucide="layout-grid" class="h-5 w-5"></i></span>
                                <span class="border-b-2 pb-1 {{ request()->routeIs('plans.*') ? 'border-ink' : 'border-transparent group-hover:border-hairline' }}">Plans</span>
                            </a>
                        @endauth
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
                            @if (auth()->user()->isDriver())
                                <a href="{{ route('driver.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('driver.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Driver Panel</a>
                            @else
                                <a href="{{ route('plans.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('plans.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Plans</a>
                                <a href="{{ route('addresses.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('addresses.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Addresses</a>
                                <a href="{{ route('payments.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('payments.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Payments</a>
                                <a href="{{ route('notifications.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('notifications.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Notifications</a>
                            @endif
                            @if (Route::has('audit-logs.index') && auth()->user()->isAdmin())
                                <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('audit-logs.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Audit logs</a>
                                <a href="{{ route('drivers.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('drivers.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Drivers</a>
                                <a href="{{ route('warehouse-staff.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('warehouse-staff.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Warehouse Staff</a>
                                <a href="{{ route('delivery-zones.index') }}" class="inline-flex items-center rounded-full border px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('delivery-zones.*') ? 'border-ink bg-ink text-white' : 'border-hairline bg-canvas text-ink hover:bg-cloud' }}">Delivery Zones</a>
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

        <footer class="mt-14 border-t border-hairline bg-[linear-gradient(180deg,#ffffff_0%,#f5f8ff_100%)]">
            <div class="air-shell py-12">
                <div class="grid gap-10 lg:grid-cols-[1.3fr_repeat(4,minmax(0,1fr))]">
                    <div class="space-y-5">
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                            <img src="{{ asset('AppIcon.png') }}" alt="Subscription Box icon" class="h-10 w-10 rounded-xl object-cover ring-1 ring-hairline">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.28em] text-rausch">Subscription Platform</p>
                                <p class="text-sm font-semibold text-ink">Subscription Box Platform</p>
                            </div>
                        </a>
                        <p class="max-w-sm text-sm leading-7 text-ash">Enterprise subscription operations for billing, fulfillment, dispatch management, and support workflows in one system.</p>
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="https://www.linkedin.com" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-hairline bg-canvas text-ink transition hover:border-ink/30 hover:bg-cloud" aria-label="LinkedIn"><i data-lucide="linkedin" class="h-4 w-4"></i></a>
                            <a href="https://www.x.com" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-hairline bg-canvas text-ink transition hover:border-ink/30 hover:bg-cloud" aria-label="X"><i data-lucide="twitter" class="h-4 w-4"></i></a>
                            <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-hairline bg-canvas text-ink transition hover:border-ink/30 hover:bg-cloud" aria-label="Instagram"><i data-lucide="instagram" class="h-4 w-4"></i></a>
                            <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="inline-flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-hairline bg-canvas text-ink transition hover:border-ink/30 hover:bg-cloud" aria-label="YouTube"><i data-lucide="youtube" class="h-4 w-4"></i></a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Product</p>
                        <div class="grid gap-2 text-sm">
                            <a href="{{ route('plans.index') }}" class="cursor-pointer text-ash transition hover:text-ink">Subscription plans</a>
                            <a href="{{ route('subscriptions.index') }}" class="cursor-pointer text-ash transition hover:text-ink">Subscription management</a>
                            <a href="{{ route('boxes.index') }}" class="cursor-pointer text-ash transition hover:text-ink">Box lifecycle</a>
                            <a href="{{ route('deliveries.index') }}" class="cursor-pointer text-ash transition hover:text-ink">Delivery tracking</a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Resources</p>
                        <div class="grid gap-2 text-sm">
                            <a href="{{ route('docs.index', ['tab' => 'resources']) }}#articles" class="cursor-pointer text-ash transition hover:text-ink">Articles</a>
                            <a href="{{ route('docs.index', ['tab' => 'resources']) }}#product-updates" class="cursor-pointer text-ash transition hover:text-ink">Product updates</a>
                            <a href="{{ route('docs.index', ['tab' => 'resources']) }}#operations-playbooks" class="cursor-pointer text-ash transition hover:text-ink">Operations playbooks</a>
                            <a href="{{ route('docs.index', ['tab' => 'resources']) }}#shipping-guides" class="cursor-pointer text-ash transition hover:text-ink">Shipping guides</a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Support</p>
                        <div class="grid gap-2 text-sm">
                            <a href="{{ route('docs.index', ['tab' => 'support']) }}#faq" class="cursor-pointer text-ash transition hover:text-ink">FAQ</a>
                            <a href="{{ route('docs.index', ['tab' => 'support']) }}#help-center" class="cursor-pointer text-ash transition hover:text-ink">Help center</a>
                            <a href="{{ route('docs.index', ['tab' => 'support']) }}#support-tickets" class="cursor-pointer text-ash transition hover:text-ink">Support tickets</a>
                            <a href="{{ route('docs.index', ['tab' => 'support']) }}#contact-email" class="cursor-pointer text-ash transition hover:text-ink">subscriptionboxplatform@gmail.com</a>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-ink">Company</p>
                        <div class="grid gap-2 text-sm">
                            <a href="{{ route('docs.index', ['tab' => 'company']) }}#about" class="cursor-pointer text-ash transition hover:text-ink">About</a>
                            <a href="{{ route('docs.index', ['tab' => 'company']) }}#careers" class="cursor-pointer text-ash transition hover:text-ink">Careers</a>
                            <a href="{{ route('docs.index', ['tab' => 'company']) }}#privacy-policy" class="cursor-pointer text-ash transition hover:text-ink">Privacy policy</a>
                            <a href="{{ route('docs.index', ['tab' => 'company']) }}#terms-of-service" class="cursor-pointer text-ash transition hover:text-ink">Terms of service</a>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex flex-col gap-3 border-t border-hairline pt-6 text-xs text-ash sm:flex-row sm:items-center sm:justify-between">
                    <p>&copy; {{ now()->year }} Subscription Box Platform. All rights reserved.</p>
                    <p>Security, compliance, and operational transparency for subscription commerce.</p>
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
