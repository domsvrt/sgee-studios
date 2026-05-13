<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGee Studios</title>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        surface: '#f7f8fb'
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="h-full bg-surface text-slate-950 antialiased">
    <main class="mx-auto flex min-h-screen max-w-5xl flex-col justify-center px-6 py-12">
        <div class="max-w-3xl">
            <div class="mb-6 grid h-12 w-12 place-items-center rounded-lg bg-slate-950 text-sm font-black text-white">SG</div>
            <p class="mb-3 text-xs font-bold uppercase tracking-wider text-teal-700">SGee Studios</p>
            <h1 class="text-5xl font-black tracking-tight text-slate-950 md:text-7xl"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mt-5 max-w-xl text-base leading-7 text-slate-600">A focused workspace for booking operations, service management, and studio administration.</p>
            <div class="mt-8">
                <a href="/admin" class="inline-flex h-11 items-center rounded-lg bg-slate-950 px-5 text-sm font-black text-white shadow-sm transition hover:bg-slate-800">Open admin</a>
            </div>
        </div>
    </main>
</body>
</html>
