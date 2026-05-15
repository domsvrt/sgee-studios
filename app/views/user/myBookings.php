<?php

declare(strict_types=1);

$bookings = $bookings ?? [];
$bookingItems = $bookingItems ?? [];
?>
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-end">
        <div>
            <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Customer workspace</p>
            <h1 class="mt-3 text-4xl font-black">My Bookings</h1>
        </div>
        <a href="/book-now" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Book another date</a>
    </div>

    <div class="mt-8 grid gap-4">
        <?php foreach ($bookings as $booking): ?>
            <?php $items = $bookingItems[(int) $booking['id']] ?? []; ?>
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col justify-between gap-4 md:flex-row md:items-start">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-xl font-black"><?= htmlspecialchars((string) $booking['booking_code'], ENT_QUOTES, 'UTF-8') ?></h2>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-black uppercase text-slate-700"><?= htmlspecialchars((string) $booking['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <p class="mt-2 text-sm font-bold text-slate-600"><?= htmlspecialchars((string) $booking['booking_date'], ENT_QUOTES, 'UTF-8') ?> at <?= htmlspecialchars(substr((string) $booking['booking_time'], 0, 5), ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-1 text-sm text-slate-500"><?= htmlspecialchars((string) ($booking['category_name'] ?? 'No category'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-xs font-black uppercase tracking-wide text-slate-500">Total</p>
                        <p class="text-2xl font-black">$<?= htmlspecialchars((string) $booking['total_amount'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="mt-1 text-xs font-semibold text-slate-500">Updated <?= htmlspecialchars((string) ($booking['updated_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <p class="text-xs font-black uppercase tracking-wide text-slate-500">Booked services</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <?php foreach ($items as $item): ?>
                            <span class="rounded-full bg-[#f6f3ee] px-3 py-1 text-xs font-bold text-slate-700"><?= htmlspecialchars((string) $item['service_name_snapshot'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endforeach; ?>
                        <?php if (!$items): ?><span class="text-sm text-slate-500">No services attached.</span><?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (!$bookings): ?>
            <div class="rounded-lg border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
                <h2 class="text-2xl font-black">No bookings yet.</h2>
                <p class="mt-2 text-sm text-slate-600">Your reserved dates and booked services will appear here.</p>
                <a href="/book-now" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-lg bg-[#c84c3a] px-5 text-sm font-black text-white">Start a booking</a>
            </div>
        <?php endif; ?>
    </div>
</section>
