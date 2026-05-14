<?php
/** @var array<int, array<string, mixed>> $logs */
$logs = $logs ?? [];
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
?>
<section class="admin-panel">
    <div class="admin-panel-header"><h3 class="admin-panel-title">Booking Status Logs</h3><p class="admin-panel-subtitle">Read-only booking status audit trail.</p></div>
    <div class="overflow-x-auto">
        <table class="admin-table min-w-[900px]">
            <thead>
                <tr><th class="px-5 py-3">ID</th><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Old</th><th class="px-5 py-3">New</th><th class="px-5 py-3">Changed By</th><th class="px-5 py-3">Note</th><th class="px-5 py-3">Created</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="px-5 py-3 font-black"><?= $e($log['id']) ?></td>
                        <td class="px-5 py-3 font-black"><?= $e($log['booking_code']) ?></td>
                        <td class="px-5 py-3"><span class="status-badge status-<?= $e($log['old_status'] ?? 'pending') ?>"><?= $e($log['old_status'] ?? 'none') ?></span></td>
                        <td class="px-5 py-3"><span class="status-badge status-<?= $e($log['new_status']) ?>"><?= $e($log['new_status']) ?></span></td>
                        <td class="px-5 py-3"><?= $e($log['changed_by_name'] ?? 'System') ?></td>
                        <td class="px-5 py-3"><?= $e($log['change_note'] ?? '') ?></td>
                        <td class="px-5 py-3"><?= $e($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$logs): ?>
                    <tr><td colspan="7" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No status logs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
