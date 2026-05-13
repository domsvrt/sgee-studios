<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title ?? 'Admin') ?> | SGee Studios</title>
    <script>
        (function () {
            var theme = localStorage.getItem('admin-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            if ((theme || (prefersDark ? 'dark' : 'light')) === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slateglass: '#111827',
                        surface: '#f7f8fb',
                        accent: '#0f766e',
                        iris: '#4f46e5'
                    },
                    boxShadow: {
                        panel: '0 18px 50px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <style>
        * { scrollbar-width: thin; scrollbar-color: #94a3b8 transparent; }
    </style>
</head>
<body class="h-full bg-surface text-slate-950 antialiased dark:bg-slate-950 dark:text-slate-100">
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
    <aside class="border-b border-slate-200 bg-white/90 px-4 py-4 backdrop-blur dark:border-slate-800 dark:bg-slate-900/95 lg:sticky lg:top-0 lg:h-screen lg:w-64 lg:border-b-0 lg:border-r">
        <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="grid h-10 w-10 place-items-center rounded-lg bg-slate-950 text-sm font-black text-white dark:bg-white dark:text-slate-950">SG</div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">SGee Studios</p>
                <h1 class="truncate text-sm font-semibold text-slate-950 dark:text-white">Operations Admin</h1>
            </div>
        </div>
        <div class="mt-4 rounded-lg border border-slate-200 bg-white p-3 text-xs leading-5 text-slate-500 dark:border-slate-800 dark:bg-slate-950/50 dark:text-slate-400">
            Manage bookings, services, categories, and users from one focused workspace.
        </div>
        <nav class="mt-4 grid gap-1">
            <?php foreach ($nav as $key => [$href, $label, $icon]): ?>
                <a href="<?= $e($href) ?>" class="group flex h-10 items-center gap-3 rounded-lg px-3 text-sm font-semibold transition <?= ($activeNav ?? '') === $key ? 'bg-slate-950 text-white shadow-sm dark:bg-white dark:text-slate-950' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' ?>">
                    <span class="grid h-6 w-6 place-items-center rounded-md text-[10px] font-black <?= ($activeNav ?? '') === $key ? 'bg-teal-500 text-white dark:bg-teal-600' : 'bg-slate-100 text-slate-500 group-hover:bg-white dark:bg-slate-800 dark:text-slate-400 dark:group-hover:bg-slate-700' ?>"><?= $e($icon) ?></span>
                    <span class="truncate"><?= $e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="min-w-0 flex-1">
        <header class="border-b border-slate-200 bg-white/80 px-4 py-5 backdrop-blur dark:border-slate-800 dark:bg-slate-950/75 md:px-8">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">Admin Management</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950 dark:text-white"><?= $e($title ?? 'Admin') ?></h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button id="theme-toggle" type="button" class="inline-flex h-10 items-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:border-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-slate-500">Dark mode</button>
                    <a href="/" class="inline-flex h-10 items-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 shadow-sm transition hover:border-slate-400 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:border-slate-500">View site</a>
                    <?php if (isset($_SESSION['admin_user_id'])): ?>
                        <form method="post" action="/admin/logout"><button class="inline-flex h-10 items-center rounded-lg bg-slate-950 px-4 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">Logout</button></form>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <?php if ($flash): ?>
            <div class="mx-auto mt-5 max-w-7xl px-4 md:px-8">
                <div class="rounded-lg border px-4 py-3 text-sm font-semibold shadow-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300' : 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300' ?>">
                    <?= $e($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>
        <section class="mx-auto max-w-7xl p-4 md:p-8">
            <?= $content ?>
        </section>
    </main>
</div>
<script>
    (function () {
        var key = 'admin-theme';
        var button = document.getElementById('theme-toggle');
        if (!button) return;
        function isDark() { return document.documentElement.classList.contains('dark'); }
        function syncLabel() { button.textContent = isDark() ? 'Light mode' : 'Dark mode'; }
        syncLabel();
        button.addEventListener('click', function () {
            document.documentElement.classList.toggle('dark');
            localStorage.setItem(key, isDark() ? 'dark' : 'light');
            syncLabel();
        });
    })();
</script>
</body>
</html>
