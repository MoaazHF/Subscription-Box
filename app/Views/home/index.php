<section class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
    <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-xl">
        <span class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs uppercase tracking-[0.2em] text-slate-200">
            Course Template
        </span>
        <h1 class="mt-4 text-4xl font-black leading-tight">
            Simple MVC starter for your Subscription Box Portal
        </h1>
        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300">
            This project is intentionally lightweight: native PHP, manual MVC, PDO singleton, session auth, and Tailwind-ready pages.
        </p>

        <div class="mt-6 flex flex-wrap gap-3">
            <a class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 transition hover:bg-slate-200" href="<?= e(app_url('register')) ?>">
                Create account
            </a>
            <a class="rounded-xl border border-white/20 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/10" href="<?= e(app_url('dashboard')) ?>">
                Open dashboard
            </a>
        </div>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-lg font-bold text-slate-900">Included in the template</h2>
        <ul class="mt-4 space-y-3 text-sm text-slate-600">
            <li>Manual MVC folders for Controllers, Models, and Views</li>
            <li>DatabaseManager singleton using PDO</li>
            <li>Register, login, logout using PHP sessions</li>
            <li>Manual RBAC check for admin-only pages</li>
            <li>Starter SQL schema for the users table</li>
        </ul>
    </div>
</section>

