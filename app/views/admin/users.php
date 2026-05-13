<?php $field = 'rounded-md border border-stone-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-clay focus:ring-2 focus:ring-clay/20'; ?>
<section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 bg-stone-50/70 px-5 py-4">
        <h3 class="text-lg font-black">Create User</h3>
        <p class="mt-1 text-sm text-stone-500">Add an admin or a booking customer account.</p>
    </div>
    <form method="post" action="/admin/users/create" class="grid gap-3 p-5 md:grid-cols-4">
        <input required name="full_name" placeholder="Full name" class="<?= $field ?>">
        <input required type="email" name="email" placeholder="Email address" class="<?= $field ?>">
        <input name="phone" placeholder="Phone" class="<?= $field ?>">
        <input required type="password" name="password" placeholder="Temporary password" class="<?= $field ?>">
        <select name="role" class="<?= $field ?>"><option value="user">User</option><option value="admin">Admin</option></select>
        <select name="admin_level" class="<?= $field ?>"><option value="staff">Staff</option><option value="manager">Manager</option><option value="owner">Owner</option></select>
        <select name="status" class="<?= $field ?>"><option value="active">Active</option><option value="inactive">Inactive</option><option value="banned">Banned</option></select>
        <button class="rounded-md bg-ink px-4 py-2 text-sm font-black text-white shadow-sm transition hover:bg-zinc-800">Create user</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="flex flex-col gap-1 border-b border-stone-200 px-5 py-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 class="text-lg font-black">User Directory</h3>
            <p class="text-sm text-stone-500"><?= $e(count($users)) ?> account records</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Admin Level</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Password</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($users as $user): ?>
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" name="full_name" value="<?= $e($user['full_name']) ?>" class="w-full min-w-40 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" name="email" value="<?= $e($user['email']) ?>" class="w-full min-w-52 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" name="phone" value="<?= $e($user['phone'] ?? '') ?>" class="w-full min-w-32 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><select form="user-<?= $e($user['id']) ?>" name="role" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option><option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option></select></td>
                        <td class="px-4 py-3"><select form="user-<?= $e($user['id']) ?>" name="admin_level" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><option value="staff" <?= $user['admin_level'] === 'staff' ? 'selected' : '' ?>>Staff</option><option value="manager" <?= $user['admin_level'] === 'manager' ? 'selected' : '' ?>>Manager</option><option value="owner" <?= $user['admin_level'] === 'owner' ? 'selected' : '' ?>>Owner</option></select></td>
                        <td class="px-4 py-3"><select form="user-<?= $e($user['id']) ?>" name="status" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option><option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option></select></td>
                        <td class="px-4 py-3"><input form="user-<?= $e($user['id']) ?>" type="password" name="password" placeholder="Leave unchanged" class="w-full min-w-40 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3">
                            <form id="user-<?= $e($user['id']) ?>" method="post" action="/admin/users/update" class="inline"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="rounded-md bg-ink px-3 py-1.5 text-xs font-black text-white">Save</button></form>
                            <form method="post" action="/admin/users/delete" class="inline" onsubmit="return confirm('Delete this user?');"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="rounded-md border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-black text-red-700">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-stone-500">No users yet. Create the first admin account above.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
