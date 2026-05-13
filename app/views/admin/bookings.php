<?php
$field = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500';
$fieldSm = 'w-full min-w-32 rounded-md border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-950 shadow-sm outline-none transition focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100';
?>
<section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <h3 class="text-base font-black text-slate-950 dark:text-white">Create Booking</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Book a registered user into a required date and time slot.</p>
    </div>
    <form method="post" action="/admin/bookings/create" class="grid gap-3 p-5 lg:grid-cols-4">
        <input name="booking_code" placeholder="Auto code if blank" class="<?= $field ?>">
        <select required name="user_id" class="<?= $field ?>">
            <option value="">Select user</option>
            <?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>"><?= $e($user['full_name']) ?> (<?= $e($user['email']) ?>)</option><?php endforeach; ?>
        </select>
        <select name="category_id" class="<?= $field ?>">
            <option value="">No category</option>
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <select name="status" class="<?= $field ?>">
            <option value="pending">Pending</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option>
        </select>
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
        <button class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">Create booking</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h3 class="text-base font-black text-slate-950 dark:text-white">Bookings</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= $e(count($bookings)) ?> scheduled records</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1500px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                <tr><th class="px-4 py-3">Code</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Time</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Services</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($bookings as $booking): ?>
                    <?php $itemRows = $bookingItems[(int) $booking['id']] ?? []; ?>
                    <tr class="align-top hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-4 py-3"><input form="booking-<?= $e($booking['id']) ?>" name="booking_code" value="<?= $e($booking['booking_code']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select form="booking-<?= $e($booking['id']) ?>" name="user_id" class="<?= $fieldSm ?> min-w-48"><?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>" <?= (int) $booking['user_id'] === (int) $user['id'] ? 'selected' : '' ?>><?= $e($user['full_name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><select form="booking-<?= $e($booking['id']) ?>" name="category_id" class="<?= $fieldSm ?> min-w-44"><option value="">No category</option><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) ($booking['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input form="booking-<?= $e($booking['id']) ?>" type="date" name="booking_date" value="<?= $e($booking['booking_date']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input form="booking-<?= $e($booking['id']) ?>" type="time" name="booking_time" value="<?= $e(substr($booking['booking_time'], 0, 5)) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select form="booking-<?= $e($booking['id']) ?>" name="status" class="<?= $fieldSm ?>">
                                <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?><option value="<?= $e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?>
                            </select></td>
                        <td class="px-4 py-3">
                            <div class="max-h-44 min-w-64 overflow-y-auto rounded-lg border border-slate-200 bg-slate-50 p-2 dark:border-slate-800 dark:bg-slate-950/40">
                                <?php foreach ($services as $service): ?>
                                    <?php $checked = in_array((int) $service['id'], array_map(static fn ($item) => (int) $item['service_id'], $itemRows), true); ?>
                                    <label class="flex items-center gap-2 py-1 text-xs"><input form="booking-<?= $e($booking['id']) ?>" type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" <?= $checked ? 'checked' : '' ?> class="h-4 w-4 rounded border-slate-300 text-teal-600"> <?= $e($service['name']) ?></label>
                                <?php endforeach; ?>
                            </div>
                            <textarea form="booking-<?= $e($booking['id']) ?>" name="notes" class="<?= $fieldSm ?> mt-2 min-w-64" placeholder="Notes"><?= $e($booking['notes'] ?? '') ?></textarea>
                        </td>
                        <td class="px-4 py-3 font-black">$<?= $e($booking['total_amount']) ?></td>
                        <td class="px-4 py-3">
                            <form id="booking-<?= $e($booking['id']) ?>" method="post" action="/admin/bookings/update" class="mb-2"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="w-full rounded-lg bg-slate-950 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 dark:bg-white dark:text-slate-950">Save</button></form>
                            <form method="post" action="/admin/bookings/status" class="mb-2 grid gap-1 rounded-lg border border-slate-200 bg-slate-50 p-2 dark:border-slate-800 dark:bg-slate-950/40"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><select name="status" class="<?= $fieldSm ?>"><?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?><option value="<?= $e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?></select><input name="change_note" placeholder="Log note" class="<?= $fieldSm ?>"><button class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">Log status</button></form>
                            <form method="post" action="/admin/bookings/delete" onsubmit="return confirm('Delete this booking?');"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="w-full rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-bold text-rose-700 shadow-sm transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/40">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <tr><td colspan="9" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No bookings yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
