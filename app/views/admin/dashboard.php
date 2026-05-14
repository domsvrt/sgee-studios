<?php
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array<string, mixed> $metrics */
$metrics = $metrics ?? [];
/** @var array<int, array<string, mixed>> $upcoming */
$upcoming = $upcoming ?? [];
/** @var array<int, array<string, mixed>> $recentLogs */
$recentLogs = $recentLogs ?? [];
/** @var array<int, array<string, mixed>> $statusCounts */
$statusCounts = $statusCounts ?? [];

$metricLabels = [
    'users' => ['Total Users', 'Registered accounts'],
    'admins' => ['Admin Users', 'Managers and owners with dashboard access'],
    'activeServices' => ['Active Services', 'Visible booking options'],
    'upcomingBookings' => ['Upcoming', 'Future dated sessions'],
    'pendingBookings' => ['Pending', 'Needs confirmation'],
    'completedBookings' => ['Completed', 'Finished bookings'],
];
?>
<div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($metrics as $label => $value): ?>
        <?php [$heading, $caption] = $metricLabels[$label] ?? [$label, '']; ?>
        <div class="metric-card">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-400"><?= $e($heading) ?></p>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= $e($caption) ?></p>
                </div>
                <span class="rounded-md border border-teal-200 bg-teal-50 px-2 py-1 text-[11px] font-black text-teal-700 dark:border-teal-900 dark:bg-teal-950/50 dark:text-teal-300">Live</span>
            </div>
            <p class="mt-5 text-5xl font-black tracking-tight text-slate-950 dark:text-white"><?= $e($value) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<div class="mt-5 grid gap-5 xl:grid-cols-[1.35fr_0.85fr]">
    <section class="admin-panel">
        <div class="admin-panel-header flex items-center justify-between gap-4">
            <div>
                <h3 class="admin-panel-title">Upcoming Bookings</h3>
                <p class="admin-panel-subtitle">Next sessions ordered by schedule.</p>
            </div>
            <a href="/admin/bookings" class="btn-secondary text-xs">Manage</a>
        </div>
        <div class="overflow-x-auto">
            <table class="admin-table min-w-[640px]">
                <thead>
                    <tr><th class="px-5 py-3">Code</th><th class="px-5 py-3">User</th><th class="px-5 py-3">Date</th><th class="px-5 py-3">Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming as $booking): ?>
                        <tr>
                            <td class="px-5 py-3 font-black"><?= $e($booking['booking_code']) ?></td>
                            <td class="px-5 py-3"><?= $e($booking['user_name']) ?></td>
                            <td class="px-5 py-3"><?= $e($booking['booking_date']) ?> <?= $e(substr($booking['booking_time'], 0, 5)) ?></td>
                            <td class="px-5 py-3"><span class="status-badge status-<?= $e($booking['status']) ?>"><?= $e($booking['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$upcoming): ?>
                        <tr><td class="px-5 py-12 text-center text-slate-500 dark:text-slate-400" colspan="4">No upcoming bookings yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-header">
            <h3 class="admin-panel-title">Recent Status Changes</h3>
            <p class="admin-panel-subtitle">Audit trail activity from bookings.</p>
        </div>
        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($recentLogs as $log): ?>
                <div class="px-5 py-4 text-sm hover:bg-slate-50 dark:hover:bg-slate-800/60">
                    <p class="font-black text-slate-900 dark:text-white"><?= $e($log['booking_code']) ?> <span class="font-medium text-slate-500 dark:text-slate-400">changed to</span> <span class="status-badge status-<?= $e($log['new_status']) ?>"><?= $e($log['new_status']) ?></span></p>
                    <p class="mt-1 text-slate-500 dark:text-slate-400"><?= $e($log['change_note'] ?: 'No note') ?> · <?= $e($log['created_at']) ?></p>
                </div>
                <?php endforeach; ?>
                <?php if (!$recentLogs): ?>
                <p class="px-5 py-12 text-center text-sm text-slate-500 dark:text-slate-400">No status changes yet.</p>
                <?php endif; ?>
        </div>
    </section>
</div>

<section class="admin-panel mt-5 p-5">
    <div>
        <h3 class="admin-panel-title">Booking Status Mix</h3>
        <p class="admin-panel-subtitle">At-a-glance distribution by workflow state.</p>
    </div>
    <div class="mt-4 flex flex-wrap gap-2">
        <?php foreach ($statusCounts as $row): ?>
            <span class="status-badge status-<?= $e($row['status']) ?>"><?= $e(ucfirst($row['status'])) ?>: <?= $e($row['total']) ?></span>
        <?php endforeach; ?>
        <?php if (!$statusCounts): ?>
            <span class="text-sm text-slate-500 dark:text-slate-400">No bookings yet.</span>
        <?php endif; ?>
    </div>
</section>
