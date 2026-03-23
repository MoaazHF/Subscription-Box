<nav class="border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4">
        <a class="text-lg font-bold text-slate-900" href="<?= e(app_url()) ?>">
            Subscription Box Portal
        </a>

        <div class="flex items-center gap-3 text-sm">
            <a class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" href="<?= e(app_url()) ?>">Home</a>

            <?php if ($currentUser): ?>
                <a class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" href="<?= e(app_url('dashboard')) ?>">Dashboard</a>

                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <a class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" href="<?= e(app_url('users')) ?>">Users</a>
                <?php endif; ?>

                <form action="<?= e(app_url('logout')) ?>" method="POST">
                    <button class="rounded-lg bg-slate-900 px-3 py-2 text-white transition hover:bg-slate-700" type="submit">Logout</button>
                </form>
            <?php else: ?>
                <a class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-slate-100 hover:text-slate-900" href="<?= e(app_url('login')) ?>">Login</a>
                <a class="rounded-lg bg-slate-900 px-3 py-2 text-white transition hover:bg-slate-700" href="<?= e(app_url('register')) ?>">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

