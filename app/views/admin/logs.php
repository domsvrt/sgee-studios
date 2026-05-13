<section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 px-5 py-4"><h3 class="text-lg font-black">Booking Status Logs</h3><p class="mt-1 text-sm text-stone-500">Read-only booking status audit trail.</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-5 py-3">Booking</th><th class="px-5 py-3">Old</th><th class="px-5 py-3">New</th><th class="px-5 py-3">Changed By</th><th class="px-5 py-3">Note</th><th class="px-5 py-3">Created</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-stone-50">
                        <td class="px-5 py-3 font-black"><?= $e($log['booking_code']) ?></td>
                        <td class="px-5 py-3"><?= $e($log['old_status'] ?? 'none') ?></td>
                        <td class="px-5 py-3"><?= $e($log['new_status']) ?></td>
                        <td class="px-5 py-3"><?= $e($log['changed_by_name'] ?? 'System') ?></td>
                        <td class="px-5 py-3"><?= $e($log['change_note'] ?? '') ?></td>
                        <td class="px-5 py-3"><?= $e($log['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$logs): ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-stone-500">No status logs yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
