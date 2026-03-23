<section class="grid gap-6 md:grid-cols-3">
    <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200 md:col-span-2">
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="mt-3 text-sm leading-7 text-slate-600">
            Welcome, <?= e($user['name'] ?? 'User') ?>. This is the main authenticated area where your team can start adding subscription plans, boxes, orders, payments, and reports.
        </p>
    </div>

    <div class="rounded-3xl bg-amber-50 p-6 shadow-sm ring-1 ring-amber-100">
        <h2 class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-700">Current role</h2>
        <p class="mt-3 text-2xl font-bold text-slate-900"><?= e($user['role'] ?? 'guest') ?></p>
        <p class="mt-2 text-sm text-slate-600">RBAC is checked manually inside controllers to match the course requirement.</p>
    </div>
</section>

