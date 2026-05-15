<?php
require_once __DIR__ . '/helpers.php';
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array<int, array<string, mixed>> $users */
$users = $users ?? [];

$field = 'field';
$fieldSm = 'field field-sm w-full min-w-36';
$fieldSmCompact = 'field field-sm w-full min-w-24';
$roles = ['user', 'manager', 'admin'];
$statuses = ['active', 'inactive', 'banned'];
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">User Directory</h3>
                <p class="admin-panel-subtitle"><?= $e(count($users)) ?> account records</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-user-form" data-show-label="Create user" data-hide-label="Hide form">Create user</button>
            </div>
        </div>
    </div>
    <form id="create-user-form" method="post" action="/admin/users/create" class="hidden grid gap-3 p-5 md:grid-cols-2 xl:grid-cols-4">
        <input required name="first_name" placeholder="First name" class="<?= $field ?>">
        <input required name="last_name" placeholder="Last name" class="<?= $field ?>">
        <input required type="email" name="email" placeholder="Email address" class="<?= $field ?>">
        <input name="phone" placeholder="Phone" class="<?= $field ?>">
        <input required type="password" name="password" placeholder="Temporary password" class="<?= $field ?>">
        <select name="role" class="<?= $field ?>"><?php admin_option_tags($roles, 'user'); ?></select>
        <select name="status" class="<?= $field ?>"><?php admin_option_tags($statuses, 'active'); ?></select>
    </form>
    <div class="overflow-x-auto">
        <table class="admin-table min-w-[1120px]">
            <thead>
                <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">First Name</th><th class="px-4 py-3">Last Name</th><th class="px-4 py-3">Email</th><th class="px-4 py-3">Phone</th><th class="px-4 py-3">Role</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Created At</th><th class="px-4 py-3">Updated At</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <?php
                    $firstName = (string) ($user['first_name'] ?? '');
                    $lastName = (string) ($user['last_name'] ?? '');
                    ?>
                    <tr>
                        <td class="px-4 py-3 font-black"><?= $e($user['id']) ?></td>
                        <td class="px-4 py-3 text-xs font-black"><?= $e($user['user_code'] ?? '') ?></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="first_name" value="<?= $e($firstName) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="last_name" value="<?= $e($lastName) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="email" value="<?= $e($user['email']) ?>" class="<?= $fieldSm ?> min-w-52"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="phone" value="<?= $e($user['phone'] ?? '') ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-3 py-3"><select data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="role" class="<?= $fieldSmCompact ?>"><?php admin_option_tags($roles, $user['role']); ?></select></td>
                        <td class="px-3 py-3"><select data-edit-field disabled form="user-<?= $e($user['id']) ?>" name="status" class="<?= $fieldSmCompact ?>"><?php admin_option_tags($statuses, $user['status']); ?></select><span class="status-badge status-<?= $e($user['status']) ?> hidden"><?= $e($user['status']) ?></span></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($user['created_at'] ?? '') ?></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($user['updated_at'] ?? '') ?></td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                                <form id="user-<?= $e($user['id']) ?>" method="post" action="/admin/users/update"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                                <form method="post" action="/admin/users/delete" onsubmit="return confirm('Delete this user?');"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="btn-danger">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <?php admin_render_empty_row(11, 'No users yet. Create the first admin account above.'); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
