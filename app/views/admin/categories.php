<?php
$field = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500';
$fieldSm = 'w-full min-w-28 rounded-md border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-950 shadow-sm outline-none transition focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100';
?>
<section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <h3 class="text-base font-black text-slate-950 dark:text-white">Create Category</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Organize services into booking groups.</p>
    </div>
    <form method="post" action="/admin/categories/create" class="grid gap-3 p-5 md:grid-cols-5">
        <input required name="slug" placeholder="slug" class="<?= $field ?>">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <input type="number" name="sort_order" value="0" class="<?= $field ?>">
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
        <button class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 md:col-span-4">Create category</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h3 class="text-base font-black text-slate-950 dark:text-white">Categories</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= $e(count($categories)) ?> service groups</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                <tr><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($categories as $category): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" name="slug" value="<?= $e($category['slug']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" name="name" value="<?= $e($category['name']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" name="description" value="<?= $e($category['description'] ?? '') ?>" class="<?= $fieldSm ?> min-w-72"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" type="number" name="sort_order" value="<?= $e($category['sort_order']) ?>" class="<?= $fieldSm ?> max-w-24"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-teal-600" type="checkbox" name="is_active" <?= (int) $category['is_active'] === 1 ? 'checked' : '' ?>></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <form id="category-<?= $e($category['id']) ?>" method="post" action="/admin/categories/update"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="rounded-lg bg-slate-950 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 dark:bg-white dark:text-slate-950">Save</button></form>
                            <form method="post" action="/admin/categories/delete" onsubmit="return confirm('Delete this category and its services?');"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-bold text-rose-700 shadow-sm transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/40">Delete</button></form>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No categories yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
