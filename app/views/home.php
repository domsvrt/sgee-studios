<?php

declare(strict_types=1);

$page = $page ?? 'home';

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
];

$isActive = static fn (string $key): string => $page === $key
    ? 'bg-slate-950 text-white'
    : 'text-slate-700 hover:bg-white hover:text-slate-950';

$services = [
    ['title' => 'Wedding Films', 'copy' => 'Full-day story coverage, highlight films, and heirloom-ready galleries.'],
    ['title' => 'Portrait Sessions', 'copy' => 'Editorial, family, maternity, graduation, and personal branding portraits.'],
    ['title' => 'Events & Launches', 'copy' => 'Clean photo and video coverage for milestones, brands, and live programs.'],
];

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
<body class="bg-[#f6f3ee] text-slate-950 antialiased">
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
            </div>

            <div class="hidden items-center gap-2 md:flex">
                <a href="/sign-in" class="rounded-lg px-4 py-2 text-sm font-black text-slate-700 transition hover:bg-white">Sign in</a>
                <a href="/sign-up" class="rounded-lg bg-[#c84c3a] px-4 py-2 text-sm font-black text-white shadow-sm transition hover:bg-[#aa3f31]">Sign up</a>
            </div>

            <details class="relative md:hidden">
                <summary class="list-none rounded-lg bg-slate-950 px-4 py-2 text-sm font-black text-white">Menu</summary>
                <div class="absolute right-0 mt-3 w-56 rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
                    <?php foreach ($navItems as $key => $item): ?>
                        <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="block rounded-md px-3 py-2 text-sm font-black <?= $page === $key ? 'bg-slate-950 text-white' : 'text-slate-700' ?>">
                            <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                    <div class="my-2 h-px bg-slate-100"></div>
                    <a href="/sign-in" class="block rounded-md px-3 py-2 text-sm font-black text-slate-700">Sign in</a>
                    <a href="/sign-up" class="block rounded-md px-3 py-2 text-sm font-black text-[#c84c3a]">Sign up</a>
                </div>
            </details>
        </nav>
    </header>

    <main>
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
                            <strong class="block text-2xl font-black">8+</strong>
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
                        <?php foreach ($services as $service): ?>
                            <article class="rounded-lg border border-slate-200 bg-[#f6f3ee] p-6 shadow-sm">
                                <h3 class="text-xl font-black"><?= htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600"><?= htmlspecialchars($service['copy'], ENT_QUOTES, 'UTF-8') ?></p>
                            </article>
                        <?php endforeach; ?>
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
        <?php elseif ($page === 'sign-in' || $page === 'sign-up'): ?>
            <?php $creating = $page === 'sign-up'; ?>
            <section class="mx-auto grid min-h-[calc(100vh-4.5rem)] max-w-6xl items-center gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8">
                <div class="hidden lg:block">
                    <img src="/assets/images/auth-wedding-detail.jpg" alt="Wedding detail photographed in warm light" class="h-[38rem] w-full rounded-lg object-cover shadow-2xl">
                </div>
                <form class="rounded-lg bg-white p-6 shadow-xl ring-1 ring-slate-200">
                    <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]"><?= $creating ? 'Create account' : 'Welcome back' ?></p>
                    <h1 class="mt-3 text-4xl font-black"><?= $creating ? 'Plan your booking with SGee Studios.' : 'Sign in to manage your booking.' ?></h1>
                    <?php if ($creating): ?>
                        <label class="mt-6 block text-sm font-black">Name<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                    <?php endif; ?>
                    <label class="mt-4 block text-sm font-black">Email<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email"></label>
                    <label class="mt-4 block text-sm font-black">Password<input class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
                    <button class="mt-5 min-h-12 w-full rounded-lg bg-slate-950 px-5 text-sm font-black text-white" type="button"><?= $creating ? 'Create account' : 'Sign in' ?></button>
                    <p class="mt-5 text-center text-sm text-slate-600">
                        <?= $creating ? 'Already have an account?' : 'New to SGee Studios?' ?>
                        <a class="font-black text-[#c84c3a]" href="<?= $creating ? '/sign-in' : '/sign-up' ?>"><?= $creating ? 'Sign in' : 'Create one' ?></a>
                    </p>
                </form>
            </section>
        <?php endif; ?>
    </main>

    <footer class="border-t border-slate-200 bg-slate-950 px-4 py-8 text-white sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col justify-between gap-4 text-sm md:flex-row md:items-center">
            <p class="font-black">SGee Studios</p>
            <p class="text-slate-300">Photography, videography, bookings, and creative coverage.</p>
            <a href="/admin" class="font-black text-[#ffd166]">Admin</a>
        </div>
    </footer>
</body>
</html>
