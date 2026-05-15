<?php

declare(strict_types=1);

$page = $page ?? 'home';
$isUser = $isUser ?? false;
$userFirstName = trim((string) ($userFirstName ?? 'User')) ?: 'User';
$userInitial = strtoupper(substr($userFirstName, 0, 1));
$userAvatarUrl = trim((string) ($userAvatarUrl ?? ''));
$notificationUnreadCount = (int) ($notificationUnreadCount ?? 0);
$recentNotifications = $recentNotifications ?? [];

$navItems = [
    'home' => ['label' => 'Home', 'href' => '/'],
    'about' => ['label' => 'About', 'href' => '/about'],
    'contact' => ['label' => 'Contact', 'href' => '/contact'],
    'book-now' => ['label' => 'Book now', 'href' => '/book-now'],
];

$titles = [
    'home' => 'SGee Studios | Photography and Videography Bookings',
    'about' => 'About SGee Studios',
    'contact' => 'Contact SGee Studios',
    'book-now' => 'Book SGee Studios',
    'sign-in' => 'Sign in | SGee Studios',
    'sign-up' => 'Create account | SGee Studios',
    'forgot-password' => 'Forgot password | SGee Studios',
    'my-bookings' => 'My Bookings | SGee Studios',
    'notifications' => 'Notifications | SGee Studios',
    'settings' => 'Settings | SGee Studios',
];

$isActive = static fn (string $key): string => $page === $key
    ? 'bg-slate-950 text-white'
    : 'text-slate-700 hover:bg-white hover:text-slate-950';

$work = [
    ['src' => '/assets/images/work-sparklers.jpg', 'alt' => 'Bride and groom walking under sparklers'],
    ['src' => '/assets/images/work-studio-camera.jpg', 'alt' => 'Camera prepared for a studio shoot'],
    ['src' => '/assets/images/work-sunset-couple.jpg', 'alt' => 'Wedding couple portrait at sunset'],
];
?>
<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titles[$page] ?? $titles['home'], ENT_QUOTES, 'UTF-8') ?></title>
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="flex min-h-screen flex-col bg-[#f6f3ee] text-slate-950 antialiased">
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-[#f6f3ee]/90 backdrop-blur-xl">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="/" class="flex items-center gap-3" aria-label="SGee Studios home">
                <span class="grid h-11 w-11 place-items-center rounded-lg bg-slate-950 text-sm font-black text-white">SG</span>
                <span>
                    <span class="block text-sm font-black uppercase tracking-[0.24em] text-slate-950">SGee</span>
                    <span class="block text-xs font-bold text-slate-500">Studios</span>
                </span>
            </a>

            <div class="hidden items-center gap-1 rounded-lg bg-white/70 p-1 shadow-sm ring-1 ring-slate-200 md:flex">
                <?php foreach ($navItems as $key => $item): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="rounded-md px-4 py-2 text-sm font-black transition <?= $isActive($key) ?>">
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($isUser): ?>
                    <a href="/my-bookings" class="rounded-md px-4 py-2 text-sm font-black transition <?= $isActive('my-bookings') ?>">My Bookings</a>
                    <a href="/notifications" class="rounded-md px-4 py-2 text-sm font-black transition <?= $isActive('notifications') ?>">Notifications<?= $notificationUnreadCount > 0 ? ' (' . htmlspecialchars((string) min($notificationUnreadCount, 99), ENT_QUOTES, 'UTF-8') . ')' : '' ?></a>
                <?php endif; ?>
            </div>

            <div class="hidden items-center gap-2 md:flex">
                <?php if ($isUser): ?>
                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-black text-slate-800 shadow-sm">
                            <span class="flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#c84c3a] text-xs text-white" style="width:32px;height:32px;">
                                <?php if ($userAvatarUrl !== ''): ?>
                                    <img src="<?= htmlspecialchars($userAvatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Profile picture" class="object-cover" style="width:32px;height:32px;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <span class="hidden items-center justify-center" style="width:32px;height:32px;"><?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php else: ?>
                                    <?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?>
                                <?php endif; ?>
                            </span>
                            <?= htmlspecialchars($userFirstName, ENT_QUOTES, 'UTF-8') ?>
                        </summary>
                        <div class="absolute right-0 mt-3 w-72 rounded-lg border border-slate-200 bg-white p-3 shadow-xl">
                            <a href="/settings" class="mb-2 block w-full rounded-md px-3 py-2 text-left text-sm font-black text-slate-700 hover:bg-slate-50">Settings</a>
                            <form method="post" action="/logout"><button class="block w-full rounded-md px-3 py-2 text-left text-sm font-black text-[#c84c3a] hover:bg-slate-50">Sign out</button></form>
                        </div>
                    </details>
                <?php else: ?>
                    <a href="/sign-in" class="rounded-lg px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-white">Sign in</a>
                    <a href="/sign-up" class="rounded-lg bg-[#c84c3a] px-4 py-2 text-sm font-black text-white shadow-sm transition hover:bg-[#aa3f31]">Sign up</a>
                <?php endif; ?>
            </div>

            <details class="relative md:hidden">
                <summary class="list-none rounded-lg bg-slate-950 px-4 py-2 text-sm font-black text-white">Menu</summary>
                <div class="absolute right-0 mt-3 w-56 rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
                    <?php foreach ($navItems as $key => $item): ?>
                        <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="block rounded-md px-3 py-2 text-sm font-black <?= $page === $key ? 'bg-slate-950 text-white' : 'text-slate-700' ?>">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                    <?php if ($isUser): ?>
                        <a href="/my-bookings" class="block rounded-md px-3 py-2 text-sm font-black <?= $page === 'my-bookings' ? 'bg-slate-950 text-white' : 'text-slate-700' ?>">My Bookings</a>
                        <a href="/notifications" class="block rounded-md px-3 py-2 text-sm font-black <?= $page === 'notifications' ? 'bg-slate-950 text-white' : 'text-slate-700' ?>">Notifications<?= $notificationUnreadCount > 0 ? ' (' . htmlspecialchars((string) min($notificationUnreadCount, 99), ENT_QUOTES, 'UTF-8') . ')' : '' ?></a>
                    <?php endif; ?>
                    <div class="my-2 h-px bg-slate-100"></div>
                    <?php if ($isUser): ?>
                        <div class="mb-1 flex items-center gap-2 px-3 py-2 text-sm font-black text-slate-800">
                            <?php if ($userAvatarUrl !== ''): ?>
                                <span class="flex shrink-0 items-center justify-center overflow-hidden rounded-full bg-[#c84c3a] text-xs text-white" style="width:32px;height:32px;">
                                    <img src="<?= htmlspecialchars($userAvatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Profile picture" class="object-cover" style="width:32px;height:32px;" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                    <span class="hidden items-center justify-center" style="width:32px;height:32px;"><?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?></span>
                                </span>
                            <?php else: ?>
                                <span class="flex shrink-0 items-center justify-center rounded-full bg-[#c84c3a] text-xs text-white" style="width:32px;height:32px;"><?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?= htmlspecialchars($userFirstName, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                        <a href="/settings" class="mb-2 block rounded-md px-3 py-2 text-sm font-black text-slate-700">Settings</a>
                        <form method="post" action="/logout"><button class="block w-full rounded-md px-3 py-2 text-left text-sm font-black text-[#c84c3a]">Sign out</button></form>
                    <?php else: ?>
                        <a href="/sign-in" class="block rounded-md px-3 py-2 text-sm font-black text-slate-700">Sign in</a>
                        <a href="/sign-up" class="block rounded-md px-3 py-2 text-sm font-black text-[#c84c3a]">Sign up</a>
                    <?php endif; ?>
                </div>
            </details>
        </nav>
    </header>

    <main class="flex-1">
        <?php if (!empty($flash['message']) && $page !== 'sign-in' && $page !== 'sign-up' && $page !== 'forgot-password'): ?>
            <div class="mx-auto max-w-7xl px-4 pt-4 sm:px-6 lg:px-8" role="alert">
                <div class="flex items-center gap-3 rounded-lg border px-4 py-3 text-sm font-bold <?= ($flash['type'] ?? '') === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' ?>">
                    <?php if (($flash['type'] ?? '') === 'success'): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <?php else: ?>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?php endif; ?>
                    <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($page === 'home'): ?>
            <section class="mx-auto grid min-h-[calc(100vh-4.5rem)] max-w-7xl items-center gap-10 px-4 py-10 sm:px-6 lg:grid-cols-[1.02fr_0.98fr] lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-black uppercase tracking-[0.28em] text-[#c84c3a]">Photo and film booking studio</p>
                    <h1 class="mt-4 text-5xl font-black leading-[0.98] text-slate-950 sm:text-6xl lg:text-7xl">Stories framed with feeling, polish, and purpose.</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">Book photography and videography for weddings, portraits, debuts, corporate launches, and intimate celebrations. SGee Studios handles the visuals from planning to final delivery.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="/book-now" class="inline-flex min-h-12 items-center justify-center rounded-lg bg-slate-950 px-6 text-sm font-black text-white shadow-lg transition hover:bg-slate-800">Reserve a date</a>
                        <a href="/about" class="inline-flex min-h-12 items-center justify-center rounded-lg border border-slate-300 bg-white/70 px-6 text-sm font-black text-slate-800 transition hover:bg-white">Meet the studio</a>
                    </div>
                    <div class="mt-10 grid max-w-2xl grid-cols-3 gap-3">
                        <div class="rounded-lg bg-white/80 p-4 shadow-sm ring-1 ring-slate-200">
                            <strong class="block text-2xl font-black">10+</strong>
                            <span class="text-xs font-bold text-slate-500">Years shooting</span>
                        </div>
                        <div class="rounded-lg bg-white/80 p-4 shadow-sm ring-1 ring-slate-200">
                            <strong class="block text-2xl font-black">500+</strong>
                            <span class="text-xs font-bold text-slate-500">Sessions covered</span>
                        </div>
                        <div class="rounded-lg bg-white/80 p-4 shadow-sm ring-1 ring-slate-200">
                            <strong class="block text-2xl font-black">4K</strong>
                            <span class="text-xs font-bold text-slate-500">Video delivery</span>
                        </div>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-5">
                    <img src="/assets/images/hero-lake-couple.jpg" alt="Wedding couple photographed by a lake" class="h-[34rem] w-full rounded-lg object-cover shadow-2xl sm:col-span-3">
                    <div class="grid gap-4 sm:col-span-2">
                        <img src="/assets/images/hero-bride-bouquet.jpg" alt="Bride holding a bouquet" class="h-64 w-full rounded-lg object-cover shadow-lg">
                        <div class="rounded-lg bg-[#253d5b] p-5 text-white shadow-lg">
                            <p class="text-sm font-black uppercase tracking-[0.22em] text-[#ffd166]">Next dates</p>
                            <p class="mt-3 text-2xl font-black leading-tight">Weekend slots fill fast. Start with a soft reservation.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="bg-white py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                        <div>
                            <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Services</p>
                            <h2 class="mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Coverage for the moments people keep.</h2>
                        </div>
                        <a href="/book-now" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Book a package</a>
                    </div>
                    <div class="mt-8 grid gap-4 md:grid-cols-3">
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $category): ?>
                                <article class="rounded-lg border border-slate-200 bg-[#f6f3ee] p-6 shadow-sm">
                                    <h3 class="text-xl font-black"><?= htmlspecialchars((string) ($category['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                                    <p class="mt-3 text-sm leading-6 text-slate-600"><?= htmlspecialchars((string) ($category['description'] ?: 'Explore this category for available services and booking options.'), ENT_QUOTES, 'UTF-8') ?></p>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <article class="rounded-lg border border-slate-200 bg-[#f6f3ee] p-6 shadow-sm md:col-span-3">
                                <h3 class="text-xl font-black">Service Categories</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">No active categories available yet.</p>
                            </article>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="py-16">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="grid gap-4 md:grid-cols-3">
                        <?php foreach ($work as $image): ?>
                            <img src="<?= htmlspecialchars($image['src'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($image['alt'], ENT_QUOTES, 'UTF-8') ?>" class="aspect-[4/5] w-full rounded-lg object-cover shadow-lg">
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php elseif ($page === 'about'): ?>
            <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">About the studio</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">A nimble creative team for honest, cinematic coverage.</h1>
                    <p class="mt-6 text-lg leading-8 text-slate-600">SGee Studios blends calm direction, natural light, clean color, and documentary timing. The result is a gallery and film that feel elevated without losing the real emotion of the day.</p>
                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                            <h2 class="font-black">Guided planning</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Shot lists, timelines, and location choices are aligned before shoot day.</p>
                        </div>
                        <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-slate-200">
                            <h2 class="font-black">Refined delivery</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Edited photos, highlight films, and social-ready cuts are prepared for easy sharing.</p>
                        </div>
                    </div>
                </div>
                <img src="/assets/images/about-videographer.jpg" alt="Videographer filming a scene with a professional camera" class="h-full min-h-[28rem] w-full rounded-lg object-cover shadow-2xl">
            </section>
        <?php elseif ($page === 'contact'): ?>
            <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Contact</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Tell us what you are planning.</h1>
                    <p class="mt-6 text-lg leading-8 text-slate-600">Send the date, location, and type of coverage you need. The studio will reply with availability and a recommended package.</p>
                    <div class="mt-8 space-y-3 text-sm font-bold text-slate-700">
                        <p>Email: hello@sgeestudios.test</p>
                        <p>Phone: +63 900 000 0000</p>
                        <p>Studio hours: Monday to Saturday, 9 AM to 6 PM</p>
                    </div>
                </div>
                <form class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="text-sm font-black">Name<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text" name="name"></label>
                        <label class="text-sm font-black">Email<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email" name="email"></label>
                    </div>
                    <label class="mt-4 block text-sm font-black">Message<textarea class="mt-2 min-h-40 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" name="message"></textarea></label>
                    <button class="mt-5 min-h-12 w-full rounded-lg bg-slate-950 px-5 text-sm font-black text-white" type="button">Send inquiry</button>
                </form>
            </section>
        <?php elseif ($page === 'book-now'): ?>
            <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Book now</p>
                    <h1 class="mt-4 text-5xl font-black leading-tight">Reserve your shoot date.</h1>
                    <p class="mt-6 text-lg leading-8 text-slate-600">Share the essentials and SGee Studios will confirm availability, package fit, and the next steps for securing the schedule.</p>
                    <img src="/assets/images/book-wedding-table.jpg" alt="Styled wedding table with flowers and candles" class="mt-8 h-72 w-full rounded-lg object-cover shadow-lg">
                </div>
                <form class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="text-sm font-black">Full name<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                        <label class="text-sm font-black">Email<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email"></label>
                        <label class="text-sm font-black">Event date<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="date"></label>
                        <label class="text-sm font-black">Coverage<select class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10"><option>Photo and video</option><option>Photography only</option><option>Videography only</option></select></label>
                    </div>
                    <label class="mt-4 block text-sm font-black">Event details<textarea class="mt-2 min-h-32 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10"></textarea></label>
                    <button class="mt-5 min-h-12 w-full rounded-lg bg-[#c84c3a] px-5 text-sm font-black text-white shadow-sm" type="button">Request booking</button>
                </form>
            </section>
        <?php elseif ($page === 'my-bookings'): ?>
            <?php
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
        <?php elseif ($page === 'notifications'): ?>
            <?php $notificationsPage = $notificationsPage ?? []; ?>
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
        <?php elseif ($page === 'settings'): ?>
            <?php $settingsUser = $settingsUser ?? ['first_name' => '', 'last_name' => '', 'email' => '']; ?>
            <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Account</p>
                    <h1 class="mt-3 text-4xl font-black">Settings</h1>
                </div>

                <div class="mt-8 grid gap-5 lg:grid-cols-2">
                    <form method="post" action="/settings/profile" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-xl font-black">Profile details</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <label class="text-sm font-black">First name<input required name="first_name" value="<?= htmlspecialchars((string) ($settingsUser['first_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                            <label class="text-sm font-black">Last name<input required name="last_name" value="<?= htmlspecialchars((string) ($settingsUser['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                        </div>
                        <label class="mt-4 block text-sm font-black">Email<input required name="email" value="<?= htmlspecialchars((string) ($settingsUser['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email"></label>
                        <button class="mt-5 min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Save profile</button>
                    </form>

                    <form method="post" action="/settings/password" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <h2 class="text-xl font-black">Change password</h2>
                        <label class="mt-4 block text-sm font-black">Current password<input required name="current_password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
                        <label class="mt-4 block text-sm font-black">New password<input required name="new_password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
                        <label class="mt-4 block text-sm font-black">Confirm new password<input required name="confirm_password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
                        <button class="mt-5 min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Update password</button>
                    </form>

                    <form method="post" action="/profile/avatar" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
                        <h2 class="text-xl font-black">Change avatar</h2>
                        <p class="mt-2 text-sm text-slate-600">Upload JPG, PNG, or WEBP up to 2MB.</p>
                        <input type="hidden" name="redirect" value="/settings">
                        <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp" class="mt-4 block w-full text-xs font-semibold text-slate-600 file:mr-2 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-black file:text-slate-700">
                        <button class="mt-4 min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Upload avatar</button>
                    </form>
                </div>
            </section>
        <?php elseif ($page === 'forgot-password'): ?>
            <section class="mx-auto grid min-h-[calc(100vh-4.5rem)] max-w-6xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="hidden lg:block">
                    <img src="/assets/images/auth-wedding-detail.jpg" alt="Wedding detail photographed in warm light" class="h-[38rem] w-full rounded-lg object-cover shadow-2xl">
                </div>
                <form method="post" action="/forgot-password" class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Recovery</p>
                    <h1 class="mt-3 text-4xl font-black">Request a password reset.</h1>
                    <?php if (!empty($flash['message'])): ?>
                        <div class="mt-4 rounded-lg border px-3 py-2 text-sm font-bold <?= ($flash['type'] ?? '') === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' ?>">
                            <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    <label class="mt-5 block text-sm font-black">Email<input required name="email" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email"></label>
                    <button class="mt-5 min-h-12 w-full rounded-lg bg-slate-950 px-5 text-sm font-black text-white" type="submit">Send request</button>
                    <p class="mt-5 text-center text-sm text-slate-600"><a class="font-black text-[#c84c3a]" href="/sign-in">Back to sign in</a></p>
                </form>
            </section>
        <?php elseif ($page === 'sign-in' || $page === 'sign-up'): ?>
            <?php $creating = $page === 'sign-up'; ?>
            <section class="mx-auto grid min-h-[calc(100vh-4.5rem)] max-w-6xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="hidden lg:block">
                    <img src="/assets/images/auth-wedding-detail.jpg" alt="Wedding detail photographed in warm light" class="h-[38rem] w-full rounded-lg object-cover shadow-2xl">
                </div>
                <form method="post" action="<?= $creating ? '/sign-up' : '/sign-in' ?>" class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]"><?= $creating ? 'Create account' : 'Welcome back' ?></p>
                    <h1 class="mt-3 text-4xl font-black"><?= $creating ? 'Plan your booking with SGee Studios.' : 'Sign in to manage your booking.' ?></h1>
                    <?php if (!empty($flash['message'])): ?>
                        <div class="mt-4 rounded-lg border px-3 py-2 text-sm font-bold <?= ($flash['type'] ?? '') === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' ?>">
                            <?= htmlspecialchars((string) $flash['message'], ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>
                    <?php if ($creating): ?>
                        <div class="mt-6 grid gap-4 sm:grid-cols-2">
                            <label class="block text-sm font-black">First name<input required name="first_name" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                            <label class="block text-sm font-black">Last name<input required name="last_name" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                        </div>
                        <label class="mt-4 block text-sm font-black">Phone number<input required name="phone" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="tel"></label>
                    <?php endif; ?>
                    <label class="mt-4 block text-sm font-black">Email<input required name="email" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email"></label>
                    <label class="mt-4 block text-sm font-black">Password
                        <span class="mt-2 flex items-center gap-2">
                            <input required name="password" class="w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password" data-auth-password-input>
                            <button type="button" class="grid h-11 w-11 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 transition hover:bg-slate-50" data-auth-password-toggle aria-label="Show password">
                                <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12a10.94 10.94 0 0 1 19.88 0 10.94 10.94 0 0 1-19.88 0Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path><path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c5 0 9.27 3.11 10.94 7a10.49 10.49 0 0 1-4.35 5.09"></path><path d="M6.61 6.61A10.49 10.49 0 0 0 1.06 12 10.94 10.94 0 0 0 12 19c1.86 0 3.62-.44 5.18-1.21"></path></svg>
                            </button>
                        </span>
                    </label>
                    <button class="mt-5 min-h-12 w-full rounded-lg bg-slate-950 px-5 text-sm font-black text-white" type="submit"><?= $creating ? 'Create account' : 'Sign in' ?></button>
                    <p class="mt-5 text-center text-sm text-slate-600">
                        <?= $creating ? 'Already have an account?' : 'New to SGee Studios?' ?>
                        <a class="font-black text-[#c84c3a]" href="<?= $creating ? '/sign-in' : '/sign-up' ?>"><?= $creating ? 'Sign in' : 'Create one' ?></a>
                    </p>
                    <?php if (!$creating): ?>
                        <p class="mt-2 text-center text-sm text-slate-600">
                            <a class="font-black text-[#c84c3a]" href="/forgot-password">Forgot password?</a>
                        </p>
                    <?php endif; ?>
                </form>
            </section>
        <?php endif; ?>
    </main>

    <footer class="border-t border-slate-200 bg-slate-950 px-4 py-8 text-white sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-4 text-sm md:flex-row md:items-center">
            <p class="font-black">SGee Studios</p>
            <p class="text-slate-300">Photography, videography, bookings, and creative coverage.</p>
        </div>
    </footer>
    <script>
        (function () {
            document.addEventListener('click', function (event) {
                var toggle = event.target.closest('[data-auth-password-toggle]');
                if (!toggle) return;
                var wrap = toggle.parentElement;
                if (!wrap) return;
                var input = wrap.querySelector('[data-auth-password-input]');
                if (!input) return;
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                var eyeOpen = toggle.querySelector('[data-eye-open]');
                var eyeClosed = toggle.querySelector('[data-eye-closed]');
                if (eyeOpen) eyeOpen.classList.toggle('hidden', show);
                if (eyeClosed) eyeClosed.classList.toggle('hidden', !show);
            });
        })();
    </script>
</body>
</html>
