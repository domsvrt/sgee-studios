<?php
require_once __DIR__ . '/helpers.php';
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array<int, array<string, mixed>> $bookings */
$bookings = $bookings ?? [];
/** @var array<int, array<string, mixed>> $users */
$users = $users ?? [];
/** @var array<int, array<string, mixed>> $categories */
$categories = $categories ?? [];
/** @var array<int, array<string, mixed>> $services */
$services = $services ?? [];
/** @var array<int, array<int, array<string, mixed>>> $bookingItems */
$bookingItems = $bookingItems ?? [];

$field = 'field';
$fieldSm = 'field field-sm w-full min-w-32';
$statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Bookings</h3>
                <p class="admin-panel-subtitle"><?= $e(count($bookings)) ?> scheduled records</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-booking-form" data-show-label="Create booking" data-hide-label="Hide form">Create booking</button>
            </div>
        </div>
    </div>
    <form id="create-booking-form" method="post" action="/admin/bookings/create" class="hidden grid gap-3 p-5 lg:grid-cols-4">
        <input name="booking_code" placeholder="Auto code if blank" class="<?= $field ?>">
        <select required name="user_id" class="<?= $field ?>">
            <option value="">Select user</option>
            <?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>"><?= $e($user['full_name']) ?> (<?= $e($user['email']) ?>)</option><?php endforeach; ?>
        </select>
        <select name="category_id" class="<?= $field ?>">
            <option value="">No category</option>
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <select name="status" class="<?= $field ?>"><?php admin_option_tags($statuses, 'pending'); ?></select>
        <input required type="date" name="booking_date" class="<?= $field ?>">
        <input required type="time" name="booking_time" class="<?= $field ?>">
        <textarea name="notes" placeholder="Notes" class="<?= $field ?> lg:col-span-2"></textarea>
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/40 lg:col-span-3">
            <p class="mb-3 text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400">Services</p>
            <div class="grid gap-2 md:grid-cols-2">
                <?php foreach ($services as $service): ?>
                    <div>
                        <label class="flex items-center justify-between gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm dark:border-slate-800 dark:bg-slate-900">
                            <span><input type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" class="mr-2 h-4 w-4 rounded border-slate-300 text-teal-600"> <?= $e($service['name']) ?></span>
                            <strong>$<?= $e($service['price']) ?></strong>
                        </label>
                    </div>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Create services before attaching booking items.</p>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <div class="overflow-x-auto">
        <table class="admin-table min-w-[1500px]">
            <thead>
                <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Time</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Services</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Created At</th><th class="px-4 py-3">Updated At</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <?php $itemRows = $bookingItems[(int) $booking['id']] ?? []; ?>
                    <tr>
                        <td class="px-4 py-3 font-black"><?= $e($booking['id']) ?></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" name="booking_code" value="<?= $e($booking['booking_code']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" name="user_id" class="<?= $fieldSm ?> min-w-48"><?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>" <?= admin_selected((int) $booking['user_id'], (int) $user['id']) ?>><?= $e($user['full_name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" name="category_id" class="<?= $fieldSm ?> min-w-44"><option value="">No category</option><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= admin_selected((int) ($booking['category_id'] ?? 0), (int) $category['id']) ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" type="date" name="booking_date" value="<?= $e($booking['booking_date']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" type="time" name="booking_time" value="<?= $e(substr($booking['booking_time'], 0, 5)) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" name="status" class="<?= $fieldSm ?>">
                                <?php admin_option_tags($statuses, $booking['status']); ?>
                            </select><span class="status-badge status-<?= $e($booking['status']) ?> hidden"><?= $e($booking['status']) ?></span></td>
                        <td class="px-4 py-3">
                            <div class="max-h-44 min-w-64 overflow-y-auto rounded-lg border border-slate-200 bg-slate-50 p-2 dark:border-slate-800 dark:bg-slate-950/40">
                                <?php foreach ($services as $service): ?>
                                    <?php $checked = in_array((int) $service['id'], array_map(static fn ($item) => (int) $item['service_id'], $itemRows), true); ?>
                                    <label class="flex items-center gap-2 py-1 text-xs"><input data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" <?= admin_checked($checked) ?> class="h-4 w-4 rounded border-slate-300 text-slate-950 dark:text-white"> <?= $e($service['name']) ?></label>
                                <?php endforeach; ?>
                            </div>
                            <textarea data-edit-field disabled form="booking-<?= $e($booking['id']) ?>" name="notes" class="<?= $fieldSm ?> mt-2 min-w-64" placeholder="Notes"><?= $e($booking['notes'] ?? '') ?></textarea>
                        </td>
                        <td class="px-4 py-3 font-black">$<?= $e($booking['total_amount']) ?></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($booking['created_at'] ?? '') ?></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($booking['updated_at'] ?? '') ?></td>
                        <td class="px-4 py-3">
                            <button type="button" data-edit-button class="btn-secondary mb-2 min-h-8 w-full px-3 py-1.5 text-xs">Edit</button>
                            <form id="booking-<?= $e($booking['id']) ?>" method="post" action="/admin/bookings/update" class="mb-2"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 w-full px-3 py-1.5 text-xs">Save</button></form>
                            <form method="post" action="/admin/bookings/status" class="mb-2 grid gap-1 rounded-lg border border-slate-200 bg-slate-50 p-2 dark:border-slate-800 dark:bg-slate-950/40"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><select name="status" class="<?= $fieldSm ?>"><?php admin_option_tags($statuses, $booking['status']); ?></select><input name="change_note" placeholder="Log note" class="<?= $fieldSm ?>"><button class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Log status</button></form>
                            <form method="post" action="/admin/bookings/delete" onsubmit="return confirm('Delete this booking?');"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="btn-danger w-full">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <?php admin_render_empty_row(12, 'No bookings yet.'); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
