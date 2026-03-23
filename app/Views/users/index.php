<section class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Users</h1>
            <p class="mt-2 text-sm text-slate-500">Simple admin-only page to prove RBAC and database integration.</p>
        </div>
    </div>

    <div class="mt-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
            <thead>
                <tr class="text-slate-500">
                    <th class="px-4 py-3 font-semibold">#</th>
                    <th class="px-4 py-3 font-semibold">Name</th>
                    <th class="px-4 py-3 font-semibold">Email</th>
                    <th class="px-4 py-3 font-semibold">Role</th>
                    <th class="px-4 py-3 font-semibold">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($users as $listedUser): ?>
                    <tr>
                        <td class="px-4 py-3 text-slate-600"><?= e((string) $listedUser['id']) ?></td>
                        <td class="px-4 py-3 font-medium text-slate-900"><?= e($listedUser['name']) ?></td>
                        <td class="px-4 py-3 text-slate-600"><?= e($listedUser['email']) ?></td>
                        <td class="px-4 py-3">
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase text-slate-700">
                                <?= e($listedUser['role']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-600"><?= e($listedUser['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

