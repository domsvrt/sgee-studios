<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | SGee Studios</title>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
</head>
<body class="min-h-screen bg-[#111111] text-zinc-100">
    <main class="grid min-h-screen place-items-center px-6 py-10">
        <section class="w-full max-w-md overflow-hidden rounded-md border border-white/10 bg-white/[0.04] shadow-2xl">
            <div class="h-1 bg-gradient-to-r from-[#b86f4b] via-[#3f5c4a] to-white"></div>
            <div class="p-6">
            <p class="text-xs font-black uppercase text-[#d89973]">SGee Studios</p>
            <h1 class="mt-3 text-3xl font-black">Admin Login</h1>
            <p class="mt-2 text-sm text-zinc-400">Sign in to manage bookings, users, services, and categories.</p>
            <?php if ($flash): ?>
                <div class="mt-5 rounded-md border border-red-800 bg-red-950 px-4 py-3 text-sm text-red-100"><?= $e($flash['message']) ?></div>
            <?php endif; ?>
            <form method="post" action="/admin/login" class="mt-6 grid gap-4">
                <label class="grid gap-2 text-sm font-bold">
                    Email
                    <input required type="email" name="email" class="rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-zinc-100 outline-none transition focus:border-[#d89973] focus:ring-2 focus:ring-[#d89973]/20">
                </label>
                <label class="grid gap-2 text-sm font-bold">
                    Password
                    <input required type="password" name="password" class="rounded-md border border-zinc-700 bg-zinc-950 px-3 py-2.5 text-zinc-100 outline-none transition focus:border-[#d89973] focus:ring-2 focus:ring-[#d89973]/20">
                </label>
                <button class="rounded-md bg-[#d89973] px-4 py-3 text-sm font-black text-zinc-950 transition hover:bg-[#e6ad8d]">Sign in</button>
            </form>
            </div>
        </section>
    </main>
</body>
</html>
