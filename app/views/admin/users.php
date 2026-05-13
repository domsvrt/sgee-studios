<section class="card">
    <div class="card-header">
        <h3 class="h5 mb-1">Create User</h3>
        <p class="small text-secondary mb-0">Add an admin or a booking customer account.</p>
    </div>
    <form method="post" action="/admin/users/create" class="card-body row g-3">
        <div class="col-md-6 col-xl-3"><input required name="full_name" placeholder="Full name" class="form-control"></div>
        <div class="col-md-6 col-xl-3"><input required type="email" name="email" placeholder="Email address" class="form-control"></div>
        <div class="col-md-6 col-xl-2"><input name="phone" placeholder="Phone" class="form-control"></div>
        <div class="col-md-6 col-xl-2"><input required type="password" name="password" placeholder="Temporary password" class="form-control"></div>
        <div class="col-md-4 col-xl-2"><select name="role" class="form-select"><option value="user">User</option><option value="admin">Admin</option></select></div>
        <div class="col-md-4 col-xl-2"><select name="admin_level" class="form-select"><option value="staff">Staff</option><option value="manager">Manager</option><option value="owner">Owner</option></select></div>
        <div class="col-md-4 col-xl-2"><select name="status" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option><option value="banned">Banned</option></select></div>
        <div class="col-md-4 col-xl-2"><button class="btn btn-primary w-100">Create user</button></div>
    </form>
</section>

<section class="card mt-3">
    <div class="card-header">
        <div>
            <h3 class="h5 mb-1">User Directory</h3>
            <p class="small text-secondary mb-0"><?= $e(count($users)) ?> account records</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Admin Level</th><th>Status</th><th>Password</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><input form="user-<?= $e($user['id']) ?>" name="full_name" value="<?= $e($user['full_name']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="user-<?= $e($user['id']) ?>" name="email" value="<?= $e($user['email']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="user-<?= $e($user['id']) ?>" name="phone" value="<?= $e($user['phone'] ?? '') ?>" class="form-control form-control-sm"></td>
                        <td><select form="user-<?= $e($user['id']) ?>" name="role" class="form-select form-select-sm"><option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option><option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option></select></td>
                        <td><select form="user-<?= $e($user['id']) ?>" name="admin_level" class="form-select form-select-sm"><option value="staff" <?= $user['admin_level'] === 'staff' ? 'selected' : '' ?>>Staff</option><option value="manager" <?= $user['admin_level'] === 'manager' ? 'selected' : '' ?>>Manager</option><option value="owner" <?= $user['admin_level'] === 'owner' ? 'selected' : '' ?>>Owner</option></select></td>
                        <td><select form="user-<?= $e($user['id']) ?>" name="status" class="form-select form-select-sm"><option value="active" <?= $user['status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= $user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option><option value="banned" <?= $user['status'] === 'banned' ? 'selected' : '' ?>>Banned</option></select></td>
                        <td><input form="user-<?= $e($user['id']) ?>" type="password" name="password" placeholder="Leave unchanged" class="form-control form-control-sm"></td>
                        <td class="text-nowrap">
                            <form id="user-<?= $e($user['id']) ?>" method="post" action="/admin/users/update" class="d-inline"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="btn btn-sm btn-primary">Save</button></form>
                            <form method="post" action="/admin/users/delete" class="d-inline" onsubmit="return confirm('Delete this user?');"><input type="hidden" name="id" value="<?= $e($user['id']) ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$users): ?>
                    <tr><td colspan="8" class="text-center text-secondary py-4">No users yet. Create the first admin account above.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
