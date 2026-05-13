<?php
$field = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500';
$fieldSm = 'w-full min-w-36 rounded-md border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-950 shadow-sm outline-none transition focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100';
$primaryBtn = 'rounded-lg bg-slate-950 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200';
$dangerBtn = 'rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-bold text-rose-700 shadow-sm transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/40';
?>
<section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <h3 class="text-base font-black text-slate-950 dark:text-white">Create User</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Add an admin or a booking customer account.</p>
    </div>
    <form method="post" action="/admin/users/create" class="grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-4">
        <input required name="full_name" placeholder="Full name" class="<?= $field ?>">
        <input required type="email" name="email" placeholder="Email address" class="<?= $field ?>">
        <input name="phone" placeholder="Phone" class="<?= $field ?>">
        <input required type="password" name="password" placeholder="Temporary password" class="<?= $field ?>">
        <select name="role" class="<?= $field ?>"><option value="user">User</option><option value="admin">Admin</option></select>
        <select name="admin_level" class="<?= $field ?>"><option value="staff">Staff</option><option value="manager">Manager</option><option value="owner">Owner</option></select>
        <select name="status" class="<?= $field ?>"><option value="active">Active</option><option value="inactive">Inactive</option><option value="banned">Banned</option></select>
        <button class="<?= $primaryBtn ?>">Create user</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
        <h3 class="text-base font-black text-slate-950 dark:text-white">User Directory</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= $e(count($users)) ?> account records</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1120px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Admin Level</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Password</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" name="full_name" value="<?= $e($user['full_name']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" name="email" value="<?= $e($user['email']) ?>" class="<?= $fieldSm ?> min-w-52"></td>
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" name="phone" value="<?= $e($user['phone'] ?? '') ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select form="user-<?= $e($user['id']) ?>" name="role" class="<?= $fieldSm ?>"><option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option><option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option></select></td>
                        <td class="px-4 py-3"><select form="user-<?= $e($user['id']) ?>" name="admin_level" class="<?= $fieldSm ?>"><option value="staff" <?= $user['admin_level'] === 'staff' ? 'selected' : '' ?>>Staff</option><option value="manager" <?= $user['admin_level'] === 'manager' ? 'selected' : '' ?>>Manager</option><option value="owner" <?= $user['admin_level'] === 'owner' ? 'selected' : '' ?>>Owner</option></select></td>
                        <td class="px-4 py-3"><select form="user-<?= $e($user['id']) ?>" name="status" class="<?= $fieldSm ?>"><option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option><option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option></select></td>
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" type="password" name="password" placeholder="Leave unchanged" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <form id="user-<?= $e($user['id']) ?>" method="post" action="/admin/users/update"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="rounded-lg bg-slate-950 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 dark:bg-white dark:text-slate-950">Save</button></form>
                                <form method="post" action="/admin/users/delete" onsubmit="return confirm('Delete this user?');"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="<?= $dangerBtn ?>">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No users yet. Create the first admin account above.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
