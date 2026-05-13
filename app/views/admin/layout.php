<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title ?? 'Admin') ?> | SGee Studios</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        ink: '#111111',
                        shell: '#f6f3ee',
                        clay: '#b86f4b',
                        moss: '#3f5c4a'
                    },
                    boxShadow: {
                        soft: '0 18px 60px rgba(17, 17, 17, 0.08)'
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="min-h-screen bg-shell text-ink antialiased">
    <?php
    $nav = [
        'dashboard' => ['/admin', 'Dashboard', 'DB'],
        'users' => ['/admin/users', 'Users', 'US'],
        'categories' => ['/admin/categories', 'Categories', 'CT'],
        'services' => ['/admin/services', 'Services', 'SV'],
        'bookings' => ['/admin/bookings', 'Bookings', 'BK'],
        'logs' => ['/admin/logs', 'Status Logs', 'LG'],
    ];
    ?>
    <div class="min-h-screen lg:flex">
        <aside class="relative overflow-hidden bg-ink px-4 py-5 text-white lg:min-h-screen lg:w-80 lg:px-5">
            <div class="absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-clay/25 to-transparent"></div>
            <div class="relative">
                <div class="flex items-center gap-3 rounded-md border border-white/10 bg-white/5 p-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-md bg-clay text-sm font-black text-white shadow-soft">SG</div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-clay">SGee Studios</p>
                        <h1 class="text-lg font-semibold">Operations Admin</h1>
                    </div>
                </div>
                <div class="mt-6 rounded-md border border-white/10 bg-white/[0.03] p-3">
                    <p class="text-xs font-semibold uppercase text-zinc-500">Workspace</p>
                    <p class="mt-1 text-sm text-zinc-300">Bookings, services, categories, and users are managed from this console.</p>
                </div>
            </div>
            <nav class="relative mt-6 grid gap-1.5">
                <?php foreach ($nav as $key => [$href, $label, $abbr]): ?>
                    <a href="<?= $e($href) ?>" class="group flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition <?= ($activeNav ?? '') === $key ? 'bg-white text-ink shadow-soft' : 'text-zinc-300 hover:bg-white/10 hover:text-white' ?>">
                        <span class="flex h-8 w-8 items-center justify-center rounded-md text-[11px] font-black <?= ($activeNav ?? '') === $key ? 'bg-clay text-white' : 'bg-white/10 text-zinc-300 group-hover:bg-white/15' ?>"><?= $e($abbr) ?></span>
                        <span><?= $e($label) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <main class="min-w-0 flex-1">
            <header class="border-b border-stone-200/80 bg-white/85 px-4 py-5 shadow-sm backdrop-blur md:px-8">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-clay">Admin Management</p>
                        <h2 class="mt-1 text-3xl font-black tracking-tight md:text-4xl"><?= $e($title ?? 'Admin') ?></h2>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="/" class="inline-flex items-center rounded-md border border-stone-300 bg-white px-4 py-2 text-sm font-bold text-ink shadow-sm transition hover:border-ink">View site</a>
                        <?php if (isset($_SESSION['admin_user_id'])): ?>
                            <form method="post" action="/admin/logout"><button class="rounded-md bg-ink px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-zinc-800">Logout</button></form>
                        <?php endif; ?>
                    </div>
                </div>
            </header>
            <?php if ($flash): ?>
                <div class="mx-auto mt-5 max-w-7xl px-4 md:px-8">
                    <div class="rounded-md border px-4 py-3 text-sm font-semibold shadow-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800' ?>">
                        <?= $e($flash['message']) ?>
                    </div>
                </div>
            <?php endif; ?>
            <section class="mx-auto max-w-7xl p-4 md:p-8">
                <?= $content ?>
            </section>
        </main>
    </div>
</body>
</html>
