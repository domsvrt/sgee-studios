<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $e($title ?? 'Admin') ?> | SGee Studios</title>
    <script>
        (function () {
            var theme = localStorage.getItem('admin-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', theme || (prefersDark ? 'dark' : 'light'));
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: var(--bs-body-bg); }
        .admin-shell { min-height: 100vh; }
        .admin-sidebar { width: 250px; }
        .admin-nav .nav-link { color: var(--bs-secondary-color); border-radius: .5rem; font-weight: 500; }
        .admin-nav .nav-link:hover { background: rgba(var(--bs-emphasis-color-rgb), .06); color: var(--bs-emphasis-color); }
        .admin-nav .nav-link.active { background: var(--bs-primary); color: #fff; }
        @media (max-width: 991.98px) { .admin-sidebar { width: 100%; } }
    </style>
</head>
<body>
<?php
$nav = [
    'dashboard' => ['/admin', 'Dashboard', 'bi-speedometer2'],
    'users' => ['/admin/users', 'Users', 'bi-people'],
    'categories' => ['/admin/categories', 'Categories', 'bi-grid'],
    'services' => ['/admin/services', 'Services', 'bi-tools'],
    'bookings' => ['/admin/bookings', 'Bookings', 'bi-calendar-check'],
    'logs' => ['/admin/logs', 'Status Logs', 'bi-journal-text'],
];
?>
<div class="admin-shell d-flex">
    <aside class="admin-sidebar border-end bg-body-tertiary p-3">
        <div class="d-flex align-items-center gap-2 border rounded p-2 mb-3">
            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center fw-bold" style="width:32px;height:32px;">SG</div>
            <div>
                <div class="small text-uppercase text-secondary fw-semibold">SGee Studios</div>
                <div class="fw-semibold">Operations Admin</div>
            </div>
        </div>
        <div class="small text-secondary border rounded p-2 mb-3">Manage bookings, services, categories, and users.</div>
        <nav class="nav admin-nav flex-column gap-1">
            <?php foreach ($nav as $key => [$href, $label, $icon]): ?>
                <a href="<?= $e($href) ?>" class="nav-link d-flex align-items-center gap-2 <?= ($activeNav ?? '') === $key ? 'active' : '' ?>">
                    <i class="bi <?= $e($icon) ?>"></i><span><?= $e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="flex-grow-1">
        <header class="border-bottom bg-body px-3 px-md-4 py-3">
            <div class="container-fluid px-0 d-flex flex-wrap justify-content-between align-items-end gap-3">
                <div>
                    <div class="small text-uppercase text-secondary fw-semibold">Admin Management</div>
                    <h1 class="h2 mb-0"><?= $e($title ?? 'Admin') ?></h1>
                </div>
                <div class="d-flex gap-2">
                    <button id="theme-toggle" type="button" class="btn btn-outline-secondary">Dark mode</button>
                    <a href="/" class="btn btn-outline-secondary">View site</a>
                    <?php if (isset($_SESSION['admin_user_id'])): ?>
                        <form method="post" action="/admin/logout"><button class="btn btn-primary">Logout</button></form>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <?php if ($flash): ?>
            <div class="container-fluid p-3 p-md-4 pb-0">
                <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-danger' ?> mb-0">
                    <?= $e($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>
        <section class="container-fluid p-3 p-md-4">
            <?= $content ?>
        </section>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var key = 'admin-theme';
        var button = document.getElementById('theme-toggle');
        if (!button) return;
        function currentTheme() { return document.documentElement.getAttribute('data-bs-theme') || 'light'; }
        function syncLabel() { button.textContent = currentTheme() === 'dark' ? 'Light mode' : 'Dark mode'; }
        syncLabel();
        button.addEventListener('click', function () {
            var next = currentTheme() === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', next);
            localStorage.setItem(key, next);
            syncLabel();
        });
    })();
</script>
</body>
</html>
