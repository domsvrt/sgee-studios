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
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-panel dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-start justify-between gap-4">
                    <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400"><?= $e($heading) ?></p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= $e($caption) ?></p>
                    </div>
                <span class="rounded-md border border-teal-200 bg-teal-50 px-2 py-1 text-[11px] font-bold text-teal-700 dark:border-teal-900 dark:bg-teal-950/50 dark:text-teal-300">Live</span>
            </div>
            <p class="mt-5 text-5xl font-black tracking-tight text-slate-950 dark:text-white"><?= $e($value) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.85fr]">
    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-5 py-4 dark:border-slate-800">
            <div>
                <h3 class="text-base font-black text-slate-950 dark:text-white">Upcoming Bookings</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Next sessions ordered by schedule.</p>
                </div>
            <a href="/admin/bookings" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 transition hover:border-slate-500 dark:border-slate-700 dark:text-slate-200 dark:hover:border-slate-500">Manage</a>
            </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                    <tr><th class="px-5 py-3">Code</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <?php foreach ($upcoming as $booking): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-5 py-3 font-black"><?= $e($booking['booking_code']) ?></td>
                            <td class="px-5 py-3"><?= $e($booking['user_name']) ?></td>
                            <td class="px-5 py-3"><?= $e($booking['booking_date']) ?> <?= $e(substr($booking['booking_time'], 0, 5)) ?></td>
                            <td class="px-5 py-3"><span class="rounded-md bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200"><?= $e($booking['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$upcoming): ?>
                        <tr><td class="px-5 py-12 text-center text-slate-500 dark:text-slate-400" colspan="4">No upcoming bookings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
        <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800">
            <h3 class="text-base font-black text-slate-950 dark:text-white">Recent Status Changes</h3>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Audit trail activity from bookings.</p>
            </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($recentLogs as $log): ?>
                <div class="px-5 py-4 text-sm hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    <p class="font-black"><?= $e($log['booking_code']) ?> <span class="font-medium text-slate-500 dark:text-slate-400">changed to</span> <?= $e($log['new_status']) ?></p>
                    <p class="mt-1 text-slate-500 dark:text-slate-400"><?= $e($log['change_note'] ?: 'No note') ?> · <?= $e($log['created_at']) ?></p>
                    </div>
                <?php endforeach; ?>
                <?php if (!$recentLogs): ?>
                <p class="px-5 py-12 text-center text-sm text-slate-500 dark:text-slate-400">No status changes yet.</p>
                <?php endif; ?>
        </div>
    </section>
</div>

<section class="mt-5 rounded-lg border border-slate-200 bg-white p-5 shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div>
        <h3 class="text-base font-black text-slate-950 dark:text-white">Booking Status Mix</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">At-a-glance distribution by workflow state.</p>
        </div>
    <div class="mt-4 flex flex-wrap gap-2">
        <?php foreach ($statusCounts as $row): ?>
            <span class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-bold text-slate-700 dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-200"><?= $e(ucfirst($row['status'])) ?>: <?= $e($row['total']) ?></span>
        <?php endforeach; ?>
        <?php if (!$statusCounts): ?>
            <span class="text-sm text-slate-500 dark:text-slate-400">No bookings yet.</span>
        <?php endif; ?>
    </div>
</section>
