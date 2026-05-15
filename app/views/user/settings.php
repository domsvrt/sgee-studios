<?php

declare(strict_types=1);

$settingsUser = $settingsUser ?? ['first_name' => '', 'last_name' => '', 'email' => ''];
?>
<section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
    <div>
        <p class="text-sm font-black uppercase tracking-[0.24em] text-[#c84c3a]">Account</p>
        <h1 class="mt-3 text-4xl font-black">Settings</h1>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-2">
        <form method="post" action="/settings/profile" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black">Profile details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <label class="text-sm font-black">First name<input required name="first_name" value="<?= htmlspecialchars((string) ($settingsUser['first_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
                <label class="text-sm font-black">Last name<input required name="last_name" value="<?= htmlspecialchars((string) ($settingsUser['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="text"></label>
            </div>
            <label class="mt-4 block text-sm font-black">Email<input required name="email" value="<?= htmlspecialchars((string) ($settingsUser['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="email"></label>
            <button class="mt-5 min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Save profile</button>
        </form>

        <form method="post" action="/settings/password" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black">Change password</h2>
            <label class="mt-4 block text-sm font-black">Current password<input required name="current_password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
            <label class="mt-4 block text-sm font-black">New password<input required name="new_password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
            <label class="mt-4 block text-sm font-black">Confirm new password<input required name="confirm_password" class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password"></label>
            <button class="mt-5 min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Update password</button>
        </form>

        <form method="post" action="/profile/avatar" enctype="multipart/form-data" class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2">
            <h2 class="text-xl font-black">Change avatar</h2>
            <p class="mt-2 text-sm text-slate-600">Upload JPG, PNG, or WEBP up to 2MB.</p>
            <input type="hidden" name="redirect" value="/settings">
            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.webp" class="mt-4 block w-full text-xs font-semibold text-slate-600 file:mr-2 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-black file:text-slate-700">
            <button class="mt-4 min-h-11 rounded-lg bg-slate-950 px-5 text-sm font-black text-white">Upload avatar</button>
        </form>
    </div>
</section>
