<?php $field = 'rounded-md border border-stone-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-clay focus:ring-2 focus:ring-clay/20'; ?>
<section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 bg-stone-50/70 px-5 py-4">
        <h3 class="text-lg font-black">Create Booking</h3>
        <p class="mt-1 text-sm text-stone-500">Book a registered user into a required date and time slot.</p>
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
        <div class="rounded-md border border-stone-200 bg-stone-50 p-4 lg:col-span-3">
            <p class="mb-3 text-xs font-black uppercase text-stone-500">Services</p>
            <div class="grid gap-2 md:grid-cols-2">
                <?php foreach ($services as $service): ?>
                    <label class="flex items-center justify-between gap-3 rounded-md border border-stone-200 bg-white px-3 py-2 text-sm shadow-sm"><span><input type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" class="mr-2"> <?= $e($service['name']) ?></span><span class="font-black">$<?= $e($service['price']) ?></span></label>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <p class="text-sm text-stone-500">Create services before attaching booking items.</p>
                <?php endif; ?>
            </div>
        </div>
        <button class="rounded-md bg-ink px-4 py-2 text-sm font-black text-white shadow-sm">Create booking</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 px-5 py-4"><h3 class="text-lg font-black">Bookings</h3><p class="mt-1 text-sm text-stone-500"><?= $e(count($bookings)) ?> scheduled records</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1500px] text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">Code</th><th class="px-4 py-3">User</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Time</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Services</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($bookings as $booking): ?>
                    <?php $itemRows = $bookingItems[(int) $booking['id']] ?? []; ?>
                    <tr class="align-top hover:bg-stone-50">
                        <td class="px-4 py-3"><input form="booking-<?= $e($booking['id']) ?>" name="booking_code" value="<?= $e($booking['booking_code']) ?>" class="w-32 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><select form="booking-<?= $e($booking['id']) ?>" name="user_id" class="w-48 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>" <?= (int) $booking['user_id'] === (int) $user['id'] ? 'selected' : '' ?>><?= $e($user['full_name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><select form="booking-<?= $e($booking['id']) ?>" name="category_id" class="w-44 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><option value="">No category</option><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) ($booking['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input form="booking-<?= $e($booking['id']) ?>" type="date" name="booking_date" value="<?= $e($booking['booking_date']) ?>" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="booking-<?= $e($booking['id']) ?>" type="time" name="booking_time" value="<?= $e(substr($booking['booking_time'], 0, 5)) ?>" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3">
                            <select form="booking-<?= $e($booking['id']) ?>" name="status" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm">
                                <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?><option value="<?= $e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <div class="grid max-h-40 min-w-64 gap-1 overflow-y-auto rounded-md border border-stone-200 bg-stone-50 p-2">
                                <?php foreach ($services as $service): ?>
                                    <?php $checked = in_array((int) $service['id'], array_map(static fn ($item) => (int) $item['service_id'], $itemRows), true); ?>
                                    <label class="flex items-center gap-2 text-xs"><input form="booking-<?= $e($booking['id']) ?>" type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" <?= $checked ? 'checked' : '' ?>> <?= $e($service['name']) ?></label>
                                <?php endforeach; ?>
                            </div>
                            <textarea form="booking-<?= $e($booking['id']) ?>" name="notes" class="mt-2 w-full rounded-md border border-stone-300 px-2 py-1.5 text-xs shadow-sm" placeholder="Notes"><?= $e($booking['notes'] ?? '') ?></textarea>
                        </td>
                        <td class="px-4 py-3 font-black">$<?= $e($booking['total_amount']) ?></td>
                        <td class="px-4 py-3">
                            <form id="booking-<?= $e($booking['id']) ?>" method="post" action="/admin/bookings/update" class="mb-2"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="rounded-md bg-ink px-3 py-1.5 text-xs font-black text-white">Save</button></form>
                            <form method="post" action="/admin/bookings/status" class="mb-2 grid gap-1 rounded-md border border-stone-200 bg-stone-50 p-2"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><select name="status" class="rounded-md border border-stone-300 px-2 py-1 text-xs"><?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?><option value="<?= $e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?></select><input name="change_note" placeholder="Log note" class="rounded-md border border-stone-300 px-2 py-1 text-xs"><button class="rounded-md border border-stone-300 bg-white px-3 py-1.5 text-xs font-black">Log status</button></form>
                            <form method="post" action="/admin/bookings/delete" onsubmit="return confirm('Delete this booking?');"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="rounded-md border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-black text-red-700">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <tr><td colspan="9" class="px-5 py-8 text-center text-stone-500">No bookings yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
