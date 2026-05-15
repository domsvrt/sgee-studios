<?php
require_once __DIR__ . '/helpers.php';
$logs = $logs ?? [];
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <h3 class="admin-panel-title">Activity Logs</h3>
        <p class="admin-panel-subtitle">Operational timeline for booking and password reset events.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="admin-table min-w-[1080px]">
            <thead>
                <tr>
                    <th class="px-4 py-3">Time</th>
                    <th class="px-4 py-3">Type</th>
                    <th class="px-4 py-3">Title</th>
                    <th class="px-4 py-3">Message</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Booking</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($log['created_at']) ?></td>
                        <td class="px-4 py-3"><span class="status-badge status-pending"><?= $e($log['type']) ?></span></td>
                        <td class="px-4 py-3 font-black"><?= $e($log['title']) ?></td>
                        <td class="px-4 py-3 text-sm"><?= $e($log['message']) ?></td>
                        <td class="px-4 py-3 text-sm"><?= $e(trim((string) ($log['user_name'] ?? '')) !== '' ? $log['user_name'] : ((string) ($log['user_email'] ?? 'System'))) ?></td>
                        <td class="px-4 py-3 text-sm"><?= $e($log['booking_code'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$logs): ?>
                    <?php admin_render_empty_row(6, 'No activity logs yet.'); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
