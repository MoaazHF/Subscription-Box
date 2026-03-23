<section class="mx-auto max-w-md rounded-3xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
    <h1 class="text-2xl font-bold text-slate-900">Register</h1>
    <p class="mt-2 text-sm text-slate-500">Create a starter account for the portal.</p>

    <form class="mt-6 space-y-4" action="<?= e(app_url('register')) ?>" method="POST">
        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700" for="name">Name</label>
            <input class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-slate-400" id="name" name="name" type="text" value="<?= old('name') ?>" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700" for="email">Email</label>
            <input class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-slate-400" id="email" name="email" type="email" value="<?= old('email') ?>" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700" for="password">Password</label>
            <input class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-slate-400" id="password" name="password" type="password" required>
        </div>

        <div>
            <label class="mb-2 block text-sm font-medium text-slate-700" for="confirm_password">Confirm password</label>
            <input class="w-full rounded-xl border border-slate-200 px-4 py-3 outline-none transition focus:border-slate-400" id="confirm_password" name="confirm_password" type="password" required>
        </div>

        <button class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-700" type="submit">
            Create account
        </button>
    </form>
</section>

