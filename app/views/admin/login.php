<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SGee Studios</title>
    <script>
        (function () {
            var theme = localStorage.getItem('admin-theme');
            var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.setAttribute('data-bs-theme', theme || (prefersDark ? 'dark' : 'light'));
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <section class="card shadow-sm w-100" style="max-width: 430px;">
            <div class="card-body p-4 p-md-5">
            <p class="small text-uppercase text-secondary fw-semibold mb-2">SGee Studios</p>
            <h1 class="h3 mb-2">Admin Login</h1>
            <p class="text-secondary mb-4">Sign in to manage bookings, users, services, and categories.</p>
            <?php if ($flash): ?>
                <div class="alert alert-danger"><?= $e($flash['message']) ?></div>
            <?php endif; ?>
            <form method="post" action="/admin/login">
                <label class="form-label fw-semibold">Email</label>
                <input required type="email" name="email" class="form-control mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input required type="password" name="password" class="form-control mb-4">
                <button class="btn btn-primary w-100">Sign in</button>
            </form>
            </div>
        </section>
    </main>
</body>
</html>
