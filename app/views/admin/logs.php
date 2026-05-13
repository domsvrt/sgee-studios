<section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h3 class="text-base font-black text-slate-950 dark:text-white">Booking Status Logs</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Read-only booking status audit trail.</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                <tr><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Old</th><th class="px-5 py-3">New</th><th class="px-5 py-3">Changed By</th><th class="px-5 py-3">Note</th><th class="px-5 py-3">Created</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-5 py-3 font-black"><?= $e($log['booking_code']) ?></td>
                        <td class="px-5 py-3"><?= $e($log['old_status'] ?? 'none') ?></td>
                        <td class="px-5 py-3"><?= $e($log['new_status']) ?></td>
                        <td class="px-5 py-3"><?= $e($log['changed_by_name'] ?? 'System') ?></td>
                        <td class="px-5 py-3"><?= $e($log['change_note'] ?? '') ?></td>
                        <td class="px-5 py-3"><?= $e($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$logs): ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No status logs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
