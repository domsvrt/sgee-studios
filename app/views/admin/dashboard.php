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
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($metrics as $label => $value): ?>
        <?php [$heading, $caption] = $metricLabels[$label] ?? [$label, '']; ?>
        <div class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
            <div class="h-1 bg-gradient-to-r from-clay via-moss to-ink"></div>
            <div class="p-5">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase text-stone-500"><?= $e($heading) ?></p>
                        <p class="mt-1 text-sm text-stone-500"><?= $e($caption) ?></p>
                    </div>
                    <span class="rounded-md bg-stone-100 px-2 py-1 text-xs font-bold text-stone-600">Live</span>
                </div>
                <p class="mt-5 text-5xl font-black tracking-tight"><?= $e($value) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-6 grid gap-5 xl:grid-cols-[1.25fr_0.9fr]">
    <section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
        <div class="flex items-center justify-between border-b border-stone-200 px-5 py-4">
            <div>
                <h3 class="text-lg font-black">Upcoming Bookings</h3>
                <p class="mt-1 text-sm text-stone-500">Next sessions ordered by schedule.</p>
            </div>
            <a href="/admin/bookings" class="rounded-md border border-stone-300 px-3 py-2 text-xs font-bold hover:border-ink">Manage</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                    <tr><th class="px-5 py-3">Code</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    <?php foreach ($upcoming as $booking): ?>
                        <tr class="hover:bg-stone-50">
                            <td class="px-5 py-3 font-black"><?= $e($booking['booking_code']) ?></td>
                            <td class="px-5 py-3"><?= $e($booking['user_name']) ?></td>
                            <td class="px-5 py-3"><?= $e($booking['booking_date']) ?> <?= $e(substr($booking['booking_time'], 0, 5)) ?></td>
                            <td class="px-5 py-3"><span class="rounded-md bg-stone-100 px-2.5 py-1 text-xs font-bold text-stone-700"><?= $e($booking['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$upcoming): ?>
                        <tr><td class="px-5 py-12 text-center text-stone-500" colspan="4">No upcoming bookings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
        <div class="border-b border-stone-200 px-5 py-4">
            <h3 class="text-lg font-black">Recent Status Changes</h3>
            <p class="mt-1 text-sm text-stone-500">Audit trail activity from bookings.</p>
        </div>
        <div class="divide-y divide-stone-100">
            <?php foreach ($recentLogs as $log): ?>
                <div class="px-5 py-4 text-sm hover:bg-stone-50">
                    <p class="font-black"><?= $e($log['booking_code']) ?> <span class="font-medium text-stone-500">changed to</span> <?= $e($log['new_status']) ?></p>
                    <p class="mt-1 text-stone-500"><?= $e($log['change_note'] ?: 'No note') ?> · <?= $e($log['created_at']) ?></p>
                </div>
            <?php endforeach; ?>
            <?php if (!$recentLogs): ?>
                <p class="px-5 py-12 text-center text-sm text-stone-500">No status changes yet.</p>
            <?php endif; ?>
        </div>
    </section>
</div>

<section class="mt-5 rounded-md border border-stone-200 bg-white p-5 shadow-soft">
    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h3 class="text-lg font-black">Booking Status Mix</h3>
            <p class="text-sm text-stone-500">At-a-glance distribution by workflow state.</p>
        </div>
    </div>
    <div class="mt-4 flex flex-wrap gap-2">
        <?php foreach ($statusCounts as $row): ?>
            <span class="rounded-md border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-bold text-ink"><?= $e(ucfirst($row['status'])) ?>: <?= $e($row['total']) ?></span>
        <?php endforeach; ?>
        <?php if (!$statusCounts): ?>
            <span class="text-sm text-stone-500">No bookings yet.</span>
        <?php endif; ?>
    </div>
</section>
