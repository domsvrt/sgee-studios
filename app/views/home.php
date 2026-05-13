<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGee Studios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    <main class="container min-vh-100 d-flex flex-column justify-content-center py-5">
        <p class="small text-uppercase text-secondary fw-semibold mb-2">SGee Studios</p>
        <h1 class="display-4 fw-semibold"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></h1>
        <div class="mt-4">
            <a href="/admin" class="btn btn-primary btn-lg">Open admin</a>
        </div>
    </main>
</body>
</html>
