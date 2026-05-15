<?php
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array{type?: string, message?: string}|null $flash */
$flash = $flash ?? null;
/** @var string $content */
$content = $content ?? '';
?>
<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title ?? 'Admin') ?> | SGee Studios</title>
    <script src="/assets/js/admin/layout.js" defer></script>
    <link href="/assets/css/shared.css" rel="stylesheet">
    <link href="/assets/css/admin.css" rel="stylesheet">
</head>
<body class="h-full admin-shell antialiased">
<?php
$nav = [
    'dashboard' => ['/admin', 'Dashboard'],
    'analytics' => ['/admin/analytics', 'Analytics'],
    'users' => ['/admin/users', 'Users'],
    'categories' => ['/admin/categories', 'Categories'],
    'services' => ['/admin/services', 'Services'],
    'bookings' => ['/admin/bookings', 'Bookings'],
    'logs' => ['/admin/logs', 'Status Logs'],
    'password-requests' => ['/admin/password-requests', 'Password Requests'],
    'activity-logs' => ['/admin/activity-logs', 'Activity Logs'],
];

$navIcon = static function (string $key): string {
    $attrs = 'class="h-4 w-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
    $icons = [
        'dashboard' => '<svg ' . $attrs . '><path d="M3 13h8V3H3v10Z"></path><path d="M13 21h8V11h-8v10Z"></path><path d="M13 3v6h8V3h-8Z"></path><path d="M3 21h8v-6H3v6Z"></path></svg>',
        'analytics' => '<svg ' . $attrs . '><path d="M3 3v18h18"></path><rect x="7" y="12" width="3" height="6"></rect><rect x="12" y="9" width="3" height="9"></rect><rect x="17" y="6" width="3" height="12"></rect></svg>',
        'users' => '<svg ' . $attrs . '><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        'categories' => '<svg ' . $attrs . '><path d="M4 4h6v6H4V4Z"></path><path d="M14 4h6v6h-6V4Z"></path><path d="M4 14h6v6H4v-6Z"></path><path d="M14 14h6v6h-6v-6Z"></path></svg>',
        'services' => '<svg ' . $attrs . '><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.1-3.1a5 5 0 0 1-6.6 6.6L7.6 19.4a2.1 2.1 0 0 1-3-3l6.6-6.6a5 5 0 0 1 6.6-6.6l-3.1 3.1Z"></path></svg>',
        'bookings' => '<svg ' . $attrs . '><path d="M8 2v4"></path><path d="M16 2v4"></path><path d="M3 10h18"></path><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="m9 16 2 2 4-4"></path></svg>',
        'logs' => '<svg ' . $attrs . '><path d="M12 8v5l3 2"></path><circle cx="12" cy="12" r="10"></circle><path d="M5 5 3 3"></path><path d="m19 5 2-2"></path></svg>',
        'password-requests' => '<svg ' . $attrs . '><path d="M4 10h16"></path><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m8 14 2 2 4-4"></path></svg>',
        'activity-logs' => '<svg ' . $attrs . '><path d="M3 3v18h18"></path><path d="m7 13 3-3 3 2 4-5"></path></svg>',
    ];

    return $icons[$key] ?? '';
};
?>
<div class="min-h-screen lg:flex">
    <aside class="admin-sidebar flex flex-col">
        <div class="admin-brand">
            <div class="admin-logo">SG</div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">SGee Studios</p>
                <h1 class="truncate text-sm font-semibold text-slate-950 dark:text-white">Operations Admin</h1>
            </div>
        </div>
        <nav class="mt-4 grid gap-1">
            <?php foreach ($nav as $key => [$href, $label]): ?>
                <a href="<?= $e($href) ?>" class="group admin-nav-link <?= ($activeNav ?? '') === $key ? 'admin-nav-link-active' : 'admin-nav-link-idle' ?>">
                    <span class="admin-nav-mark <?= ($activeNav ?? '') === $key ? 'admin-nav-mark-active' : 'admin-nav-mark-idle' ?>"><?= $navIcon($key) ?></span>
                    <span class="truncate"><?= $e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="min-w-0 flex-1">
        <header class="admin-topbar">
            <div class="flex w-full flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-300">Admin Workspace</p>
                    <h2 class="mt-1 text-3xl font-black tracking-tight text-slate-950 dark:text-white"><?= $e($title ?? 'Admin') ?></h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button id="theme-toggle" type="button" class="btn-secondary">Dark mode</button>
                    <button id="logout-toggle-btn" type="button" class="btn-primary" onclick="var p=document.getElementById('logout-confirm-inline');var b=document.getElementById('logout-toggle-btn');if(p&&b){p.classList.remove('hidden');b.classList.add('hidden');}">Logout</button>
                    <div id="logout-confirm-inline" class="hidden flex items-center gap-2">
                        <form method="post" action="/logout">
                            <button class="btn-primary" style="background-color:#dc2626;border-color:#dc2626;">Yes, logout</button>
                        </form>
                        <button type="button" class="btn-secondary" onclick="var p=document.getElementById('logout-confirm-inline');var b=document.getElementById('logout-toggle-btn');if(p&&b){p.classList.add('hidden');b.classList.remove('hidden');}">Cancel</button>
                    </div>
                </div>
            </div>
        </header>
        <?php if ($flash): ?>
            <div id="admin-feedback-wrap" class="mt-5 w-full px-4 md:px-8">
                <div id="admin-feedback" class="overflow-hidden rounded-lg border pt-0 shadow-sm <?= $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300' : 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-950/40 dark:text-rose-300' ?>">
                    <div class="flex items-start justify-between gap-2 px-3 py-2 text-sm font-semibold">
                        <p><?= $e($flash['message']) ?></p>
                        <button type="button" class="text-xs font-black opacity-70 hover:opacity-100" onclick="var f=document.getElementById('admin-feedback-wrap');if(f){f.remove();}">✕</button>
                    </div>
                    <div class="h-1 w-full bg-black/10 dark:bg-white/20">
                        <div id="admin-feedback-progress" class="h-full bg-current opacity-70"></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <section class="w-full p-4 md:p-8">
            <?= $content ?>
        </section>
    </main>
</div>
 
</body>
</html>
