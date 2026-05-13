<section class="card">
    <div class="card-header"><h3 class="h5 mb-1">Booking Status Logs</h3><p class="small text-secondary mb-0">Read-only booking status audit trail.</p></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Booking</th><th>Old</th><th>New</th><th>Changed By</th><th>Note</th><th>Created</th></tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td class="fw-semibold"><?= $e($log['booking_code']) ?></td>
                        <td><?= $e($log['old_status'] ?? 'none') ?></td>
                        <td><?= $e($log['new_status']) ?></td>
                        <td><?= $e($log['changed_by_name'] ?? 'System') ?></td>
                        <td><?= $e($log['change_note'] ?? '') ?></td>
                        <td><?= $e($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$logs): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">No status logs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
