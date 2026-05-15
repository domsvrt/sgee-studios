<?php
require_once __DIR__ . '/helpers.php';
$requests = $requests ?? [];
$statusFilter = $statusFilter ?? '';
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$statuses = ['', 'pending', 'approved', 'rejected', 'completed'];
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <h3 class="admin-panel-title">Password Reset Requests</h3>
        <p class="admin-panel-subtitle">Review and process forgot-password submissions from users.</p>
    </div>
    <div class="p-5">
        <form method="get" action="/admin/password-requests" class="mb-4 flex flex-wrap items-end gap-3">
            <label class="text-xs font-bold uppercase tracking-wide text-slate-500">Status
                <select name="status" class="mt-1 rounded-md border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700">
                    <?php foreach ($statuses as $status): ?>
                        <option value="<?= $e($status) ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status === '' ? 'All' : ucfirst($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="btn-primary min-h-10 px-4 text-xs">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="admin-table min-w-[1100px]">
                <thead>
                    <tr>
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">Requested</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Notes</th>
                        <th class="px-4 py-3">Temporary Password</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="px-4 py-3 font-black"><?= $e($request['id']) ?></td>
                            <td class="px-4 py-3"><?= $e($request['email_snapshot']) ?></td>
                            <td class="px-4 py-3"><?= $e(($request['requester_name'] ?? '') !== '' ? $request['requester_name'] : 'Not linked') ?></td>
                            <td class="px-4 py-3 text-xs"><?= $e($request['requested_at']) ?></td>
                            <td class="px-4 py-3">
                                <form method="post" action="/admin/password-requests/update" class="grid gap-2">
                                    <input type="hidden" name="id" value="<?= $e($request['id']) ?>">
                                    <select name="status" class="rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs">
                                        <?php foreach (array_slice($statuses, 1) as $status): ?>
                                            <option value="<?= $e($status) ?>" <?= ($request['status'] ?? '') === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                            </td>
                            <td class="px-4 py-3">
                                    <input type="text" name="notes" value="<?= $e($request['notes'] ?? '') ?>" placeholder="Optional note" class="w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs">
                            </td>
                            <td class="px-4 py-3">
                                    <input type="text" name="temporary_password" placeholder="Only for completed" class="w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-xs">
                            </td>
                            <td class="px-4 py-3">
                                    <button class="btn-primary min-h-9 px-3 py-1 text-xs">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$requests): ?>
                        <?php admin_render_empty_row(8, 'No password reset requests found.'); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
