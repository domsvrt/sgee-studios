<?php

declare(strict_types=1);

$notificationsPage = $notificationsPage ?? [];
?>
<section class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Updates</p>
            <h1 class="mt-3 text-4xl font-black">Notifications</h1>
        </div>
        <?php if ($notificationUnreadCount > 0): ?>
            <form method="post" action="/notifications/read-all"><input type="hidden" name="redirect" value="/notifications"><button class="min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Mark all read</button></form>
        <?php endif; ?>
    </div>
    <div class="mt-8 grid gap-3">
        <?php foreach ($notificationsPage as $notification): ?>
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm <?= (int) ($notification['is_read'] ?? 0) === 0 ? 'ring-2 ring-[#c84c3a]/10' : '' ?>">
                <div class="flex flex-col justify-between gap-3 md:flex-row md:items-start">
                    <div>
                        <h2 class="text-lg font-black"><?= htmlspecialchars((string) $notification['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600"><?= htmlspecialchars((string) $notification['message'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-2 text-xs font-semibold text-slate-500"><?= htmlspecialchars((string) $notification['created_at'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <?php if ((int) ($notification['is_read'] ?? 0) === 0): ?>
                        <form method="post" action="/notifications/read"><input type="hidden" name="id" value="<?= htmlspecialchars((string) $notification['id'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="redirect" value="/notifications"><button class="min-h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs font-black text-slate-700">Mark read</button></form>
                    <?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!$notificationsPage): ?>
            <div class="rounded-lg border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
                <h2 class="text-2xl font-black">No notifications yet.</h2>
                <p class="mt-2 text-sm text-slate-600">Booking status updates will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
