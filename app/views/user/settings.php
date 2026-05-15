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
            <label class="mt-4 block text-sm font-black">Current password
                <span class="mt-2 flex items-center gap-2">
                    <input required name="current_password" class="w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password" data-auth-password-input>
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 transition hover:bg-slate-50" data-auth-password-toggle aria-label="Show password">
                        <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12a10.94 10.94 0 0 1 19.88 0 10.94 10.94 0 0 1-19.88 0Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path><path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c5 0 9.27 3.11 10.94 7a10.49 10.49 0 0 1-4.35 5.09"></path><path d="M6.61 6.61A10.49 10.49 0 0 0 1.06 12 10.94 10.94 0 0 0 12 19c1.86 0 3.62-.44 5.18-1.21"></path></svg>
                    </button>
                </span>
            </label>
            <label class="mt-4 block text-sm font-black">New password
                <span class="mt-2 flex items-center gap-2">
                    <input required name="new_password" class="w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password" data-auth-password-input>
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 transition hover:bg-slate-50" data-auth-password-toggle aria-label="Show password">
                        <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12a10.94 10.94 0 0 1 19.88 0 10.94 10.94 0 0 1-19.88 0Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path><path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c5 0 9.27 3.11 10.94 7a10.49 10.49 0 0 1-4.35 5.09"></path><path d="M6.61 6.61A10.49 10.49 0 0 0 1.06 12 10.94 10.94 0 0 0 12 19c1.86 0 3.62-.44 5.18-1.21"></path></svg>
                    </button>
                </span>
            </label>
            <label class="mt-4 block text-sm font-black">Confirm new password
                <span class="mt-2 flex items-center gap-2">
                    <input required name="confirm_password" class="w-full rounded-lg border border-slate-300 px-3 py-3 font-normal outline-none focus:border-[#c84c3a] focus:ring-4 focus:ring-[#c84c3a]/10" type="password" data-auth-password-input>
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-lg border border-slate-300 bg-white text-slate-700 transition hover:bg-slate-50" data-auth-password-toggle aria-label="Show password">
                        <svg data-eye-open xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12a10.94 10.94 0 0 1 19.88 0 10.94 10.94 0 0 1-19.88 0Z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                        <svg data-eye-closed xmlns="http://www.w3.org/2000/svg" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 3 18 18"></path><path d="M10.58 10.58a2 2 0 1 0 2.83 2.83"></path><path d="M9.88 5.09A10.94 10.94 0 0 1 12 5c5 0 9.27 3.11 10.94 7a10.49 10.49 0 0 1-4.35 5.09"></path><path d="M6.61 6.61A10.49 10.49 0 0 0 1.06 12 10.94 10.94 0 0 0 12 19c1.86 0 3.62-.44 5.18-1.21"></path></svg>
                    </button>
                </span>
            </label>
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
