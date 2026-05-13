<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGee Studios</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="min-h-screen bg-zinc-950 text-zinc-100">
    <main class="mx-auto flex min-h-screen max-w-4xl flex-col justify-center px-6">
        <p class="mb-3 text-sm uppercase tracking-[0.3em] text-amber-300">SGee Studios</p>
        <h1 class="text-4xl font-semibold tracking-tight sm:text-6xl"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></h1>
        <a href="/admin" class="mt-8 inline-flex w-fit items-center rounded-md bg-amber-300 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-amber-200">Open admin</a>
    </main>
</body>
</html>
