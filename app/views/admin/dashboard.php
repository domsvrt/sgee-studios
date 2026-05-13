<?php
$metricLabels = [
    'users' => ['Total Users', 'Registered accounts'],
    'admins' => ['Admin Users', 'Staff with dashboard access'],
    'activeServices' => ['Active Services', 'Visible booking options'],
    'upcomingBookings' => ['Upcoming', 'Future dated sessions'],
    'pendingBookings' => ['Pending', 'Needs confirmation'],
    'completedBookings' => ['Completed', 'Finished bookings'],
];
?>
<div class="row g-3">
    <?php foreach ($metrics as $label => $value): ?>
        <?php [$heading, $caption] = $metricLabels[$label] ?? [$label, '']; ?>
        <div class="col-md-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                            <div class="small text-uppercase text-secondary fw-semibold"><?= $e($heading) ?></div>
                            <div class="small text-secondary"><?= $e($caption) ?></div>
                        </div>
                        <span class="badge text-bg-light border">Live</span>
                    </div>
                    <div class="display-5 fw-semibold mt-4"><?= $e($value) ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mt-1">
    <section class="col-xl-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                    <h3 class="h5 mb-1">Upcoming Bookings</h3>
                    <p class="small text-secondary mb-0">Next sessions ordered by schedule.</p>
                </div>
                <a href="/admin/bookings" class="btn btn-sm btn-outline-secondary">Manage</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr><th>Code</th><th>User</th><th>Date</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming as $booking): ?>
                        <tr>
                            <td class="fw-semibold"><?= $e($booking['booking_code']) ?></td>
                            <td><?= $e($booking['user_name']) ?></td>
                            <td><?= $e($booking['booking_date']) ?> <?= $e(substr($booking['booking_time'], 0, 5)) ?></td>
                            <td><span class="badge text-bg-secondary"><?= $e($booking['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$upcoming): ?>
                        <tr><td class="py-4 text-center text-secondary" colspan="4">No upcoming bookings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div></section>

    <section class="col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="h5 mb-1">Recent Status Changes</h3>
                <p class="small text-secondary mb-0">Audit trail activity from bookings.</p>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($recentLogs as $log): ?>
                    <div class="list-group-item">
                        <p class="mb-1"><strong><?= $e($log['booking_code']) ?></strong> <span class="text-secondary">changed to</span> <?= $e($log['new_status']) ?></p>
                        <p class="mb-0 small text-secondary"><?= $e($log['change_note'] ?: 'No note') ?> · <?= $e($log['created_at']) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (!$recentLogs): ?>
                    <p class="list-group-item text-center text-secondary py-4 mb-0">No status changes yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<section class="card mt-3">
    <div class="card-body">
        <div class="mb-3">
            <h3 class="h5 mb-1">Booking Status Mix</h3>
            <p class="small text-secondary mb-0">At-a-glance distribution by workflow state.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
        <?php foreach ($statusCounts as $row): ?>
                <span class="badge text-bg-light border px-3 py-2"><?= $e(ucfirst($row['status'])) ?>: <?= $e($row['total']) ?></span>
        <?php endforeach; ?>
        <?php if (!$statusCounts): ?>
                <span class="small text-secondary">No bookings yet.</span>
        <?php endif; ?>
        </div>
    </div>
</section>
