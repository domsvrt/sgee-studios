<section class="card">
    <div class="card-header">
        <h3 class="h5 mb-1">Create Booking</h3>
        <p class="small text-secondary mb-0">Book a registered user into a required date and time slot.</p>
    </div>
    <form method="post" action="/admin/bookings/create" class="card-body row g-3">
        <div class="col-lg-3"><input name="booking_code" placeholder="Auto code if blank" class="form-control"></div>
        <div class="col-lg-3"><select required name="user_id" class="form-select">
            <option value="">Select user</option>
            <?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>"><?= $e($user['full_name']) ?> (<?= $e($user['email']) ?>)</option><?php endforeach; ?>
        </select></div>
        <div class="col-lg-3"><select name="category_id" class="form-select">
            <option value="">No category</option>
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="col-lg-3"><select name="status" class="form-select">
            <option value="pending">Pending</option><option value="confirmed">Confirmed</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option>
        </select></div>
        <div class="col-lg-2"><input required type="date" name="booking_date" class="form-control"></div>
        <div class="col-lg-2"><input required type="time" name="booking_time" class="form-control"></div>
        <div class="col-lg-8"><textarea name="notes" placeholder="Notes" class="form-control"></textarea></div>
        <div class="col-lg-9 border rounded p-3">
            <p class="small text-uppercase fw-semibold text-secondary mb-2">Services</p>
            <div class="row g-2">
                <?php foreach ($services as $service): ?>
                    <div class="col-md-6">
                        <label class="border rounded p-2 d-flex justify-content-between align-items-center">
                            <span><input type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" class="form-check-input me-2"> <?= $e($service['name']) ?></span>
                            <strong>$<?= $e($service['price']) ?></strong>
                        </label>
                    </div>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <p class="small text-secondary">Create services before attaching booking items.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-lg-3"><button class="btn btn-primary w-100">Create booking</button></div>
    </form>
</section>

<section class="card mt-3">
    <div class="card-header"><h3 class="h5 mb-1">Bookings</h3><p class="small text-secondary mb-0"><?= $e(count($bookings)) ?> scheduled records</p></div>
    <div class="table-responsive">
        <table class="table table-hover align-top mb-0">
            <thead>
                <tr><th>Code</th><th>User</th><th>Category</th><th>Date</th><th>Time</th><th>Status</th><th>Services</th><th>Total</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking): ?>
                    <?php $itemRows = $bookingItems[(int) $booking['id']] ?? []; ?>
                    <tr>
                        <td><input form="booking-<?= $e($booking['id']) ?>" name="booking_code" value="<?= $e($booking['booking_code']) ?>" class="form-control form-control-sm"></td>
                        <td><select form="booking-<?= $e($booking['id']) ?>" name="user_id" class="form-select form-select-sm"><?php foreach ($users as $user): ?><option value="<?= $e($user['id']) ?>" <?= (int) $booking['user_id'] === (int) $user['id'] ? 'selected' : '' ?>><?= $e($user['full_name']) ?></option><?php endforeach; ?></select></td>
                        <td><select form="booking-<?= $e($booking['id']) ?>" name="category_id" class="form-select form-select-sm"><option value="">No category</option><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) ($booking['category_id'] ?? 0) === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td><input form="booking-<?= $e($booking['id']) ?>" type="date" name="booking_date" value="<?= $e($booking['booking_date']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="booking-<?= $e($booking['id']) ?>" type="time" name="booking_time" value="<?= $e(substr($booking['booking_time'], 0, 5)) ?>" class="form-control form-control-sm"></td>
                        <td><select form="booking-<?= $e($booking['id']) ?>" name="status" class="form-select form-select-sm">
                                <?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?><option value="<?= $e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?>
                            </select></td>
                        <td>
                            <div class="border rounded p-2 mb-2" style="max-height:170px;overflow-y:auto;min-width:240px;">
                                <?php foreach ($services as $service): ?>
                                    <?php $checked = in_array((int) $service['id'], array_map(static fn ($item) => (int) $item['service_id'], $itemRows), true); ?>
                                    <label class="d-block small"><input form="booking-<?= $e($booking['id']) ?>" type="checkbox" name="service_ids[]" value="<?= $e($service['id']) ?>" <?= $checked ? 'checked' : '' ?> class="form-check-input me-1"> <?= $e($service['name']) ?></label>
                                <?php endforeach; ?>
                            </div>
                            <textarea form="booking-<?= $e($booking['id']) ?>" name="notes" class="form-control form-control-sm" placeholder="Notes"><?= $e($booking['notes'] ?? '') ?></textarea>
                        </td>
                        <td class="fw-semibold">$<?= $e($booking['total_amount']) ?></td>
                        <td class="text-nowrap">
                            <form id="booking-<?= $e($booking['id']) ?>" method="post" action="/admin/bookings/update" class="mb-2"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="btn btn-sm btn-primary w-100">Save</button></form>
                            <form method="post" action="/admin/bookings/status" class="mb-2 border rounded p-2"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><select name="status" class="form-select form-select-sm mb-1"><?php foreach (['pending', 'confirmed', 'completed', 'cancelled'] as $status): ?><option value="<?= $e($status) ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= $e(ucfirst($status)) ?></option><?php endforeach; ?></select><input name="change_note" placeholder="Log note" class="form-control form-control-sm mb-1"><button class="btn btn-sm btn-outline-secondary w-100">Log status</button></form>
                            <form method="post" action="/admin/bookings/delete" onsubmit="return confirm('Delete this booking?');"><input type="hidden" name="id" value="<?= $e($booking['id']) ?>"><button class="btn btn-sm btn-outline-danger w-100">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <tr><td colspan="9" class="text-center text-secondary py-4">No bookings yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
