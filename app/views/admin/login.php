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
    <link href="/assets/css/app.css" rel="stylesheet">
</head>
<body class="h-full admin-shell antialiased">
    <main class="grid min-h-screen place-items-center px-4 py-10">
        <section class="admin-panel w-full max-w-md">
            <div class="admin-panel-header px-6 py-6">
                <div class="admin-logo mb-4">SG</div>
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
                    <input required type="email" name="email" class="field py-2.5">
                </label>
                <label class="grid gap-2 text-sm font-bold text-slate-700 dark:text-slate-200">
                    Password
                    <input required type="password" name="password" class="field py-2.5">
                </label>
                <button class="btn-primary py-3">Sign in</button>
            </form>
            </div>
        </section>
    </main>
</body>
</html>
