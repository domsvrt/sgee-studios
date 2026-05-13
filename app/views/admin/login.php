<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SGee Studios</title>
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
                        surface: '#f7f8fb'
                    },
                    boxShadow: {
                        panel: '0 18px 50px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="h-full bg-surface text-slate-950 antialiased dark:bg-slate-950 dark:text-slate-100">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <section class="w-full max-w-md overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
            <div class="border-b border-slate-200 bg-slate-50/70 px-6 py-5 dark:border-slate-800 dark:bg-slate-950/40">
                <div class="mb-4 grid h-11 w-11 place-items-center rounded-lg bg-slate-950 text-sm font-black text-white dark:bg-white dark:text-slate-950">SG</div>
                <p class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">SGee Studios</p>
                <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 dark:text-white">Admin Login</h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Sign in to manage bookings, users, services, and categories.</p>
            </div>
            <div class="p-6">
            <?php if ($flash): ?>
                <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300"><?= $e($flash['message']) ?></div>
            <?php endif; ?>
            <form method="post" action="/admin/login" class="grid gap-4">
                <label class="grid gap-2 text-sm font-bold text-slate-700 dark:text-slate-200">
                    Email
                    <input required type="email" name="email" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-950 shadow-sm outline-none transition focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700 dark:text-slate-200">
                    Password
                    <input required type="password" name="password" class="rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-slate-950 shadow-sm outline-none transition focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100">
                </label>
                <button class="rounded-lg bg-slate-950 px-4 py-3 text-sm font-black text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">Sign in</button>
            </form>
            </div>
        </section>
    </main>
</body>
</html>
