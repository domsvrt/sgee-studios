<?php
$field = 'field';
$fieldSm = 'field field-sm w-full min-w-36';
$primaryBtn = 'btn-primary';
$dangerBtn = 'btn-danger';
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Create User</h3>
                <p class="admin-panel-subtitle">Add an admin or a booking customer account.</p>
            </div>
            <button type="button" class="btn-secondary" data-create-toggle data-target="create-user-form" data-show-label="Create user" data-hide-label="Hide form">Create user</button>
        </div>
    </div>
    <form id="create-user-form" method="post" action="/admin/users/create" class="hidden grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-4">
        <input required name="full_name" placeholder="Full name" class="<?= $field ?>">
        <input required type="email" name="email" placeholder="Email address" class="<?= $field ?>">
        <input name="phone" placeholder="Phone" class="<?= $field ?>">
        <div class="flex gap-2">
            <input required type="password" name="password" placeholder="Temporary password" class="<?= $field ?>" data-password-input>
            <button type="button" class="btn-secondary min-h-11 px-3" data-password-toggle>Show</button>
        </div>
        <select name="role" class="<?= $field ?>"><option value="user">User</option><option value="manager">Manager</option><option value="admin">Admin</option></select>
        <select name="status" class="<?= $field ?>"><option value="active">Active</option><option value="inactive">Inactive</option><option value="banned">Banned</option></select>
        <button class="<?= $primaryBtn ?>">Create user</button>
    </form>
</section>

<section class="admin-panel mt-5">
    <div class="admin-panel-header">
        <h3 class="admin-panel-title">User Directory</h3>
        <p class="admin-panel-subtitle"><?= $e(count($users)) ?> account records</p>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table min-w-[1120px]">
            <thead>
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Password</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="full_name" value="<?= $e($user['full_name']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="email" value="<?= $e($user['email']) ?>" class="<?= $fieldSm ?> min-w-52"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="phone" value="<?= $e($user['phone'] ?? '') ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="role" class="<?= $fieldSm ?>"><option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option><option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option><option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option></select></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="status" class="<?= $fieldSm ?>"><option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option><option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option></select><span class="status-badge status-<?= $e($user['status']) ?> hidden"><?= $e($user['status']) ?></span></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <input data-edit-field disabled form="user-<?= $e($user['id']) ?>" type="password" name="password" value="" placeholder="Enter new password" class="<?= $fieldSm ?>" data-password-input>
                                <button type="button" class="btn-secondary min-h-8 px-3 py-1.5 text-xs" data-password-toggle>Show</button>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                                <form id="user-<?= $e($user['id']) ?>" method="post" action="/admin/users/update"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                                <form method="post" action="/admin/users/delete" onsubmit="return confirm('Delete this user?');"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="<?= $dangerBtn ?>">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No users yet. Create the first admin account above.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    (function () {
        document.addEventListener('click', function (event) {
            var toggle = event.target.closest('[data-password-toggle]');
            if (!toggle) return;
            var wrap = toggle.parentElement;
            if (!wrap) return;
            var input = wrap.querySelector('[data-password-input]');
            if (!input) return;
            var reveal = input.type === 'password';
            input.type = reveal ? 'text' : 'password';
            toggle.textContent = reveal ? 'Hide' : 'Show';
        });
    })();
</script>
