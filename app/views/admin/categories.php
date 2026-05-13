<?php $field = 'rounded-md border border-stone-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-clay focus:ring-2 focus:ring-clay/20'; ?>
<section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 bg-stone-50/70 px-5 py-4">
        <h3 class="text-lg font-black">Create Category</h3>
        <p class="mt-1 text-sm text-stone-500">Organize services into booking groups.</p>
    </div>
    <form method="post" action="/admin/categories/create" class="grid gap-3 p-5 md:grid-cols-5">
        <input required name="slug" placeholder="slug" class="<?= $field ?>">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <input type="number" name="sort_order" value="0" class="<?= $field ?>">
        <label class="flex items-center gap-2 rounded-md border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-semibold"><input type="checkbox" name="is_active" checked> Active</label>
        <button class="rounded-md bg-ink px-4 py-2 text-sm font-black text-white shadow-sm md:col-span-4">Create category</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 px-5 py-4"><h3 class="text-lg font-black">Categories</h3><p class="mt-1 text-sm text-stone-500"><?= $e(count($categories)) ?> service groups</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">Slug</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($categories as $category): ?>
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" name="slug" value="<?= $e($category['slug']) ?>" class="w-full rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" name="name" value="<?= $e($category['name']) ?>" class="w-full rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" name="description" value="<?= $e($category['description'] ?? '') ?>" class="w-full rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" type="number" name="sort_order" value="<?= $e($category['sort_order']) ?>" class="w-20 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="category-<?= $e($category['id']) ?>" type="checkbox" name="is_active" <?= (int) $category['is_active'] === 1 ? 'checked' : '' ?>></td>
                        <td class="px-4 py-3">
                            <form id="category-<?= $e($category['id']) ?>" method="post" action="/admin/categories/update" class="inline"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="rounded-md bg-ink px-3 py-1.5 text-xs font-black text-white">Save</button></form>
                            <form method="post" action="/admin/categories/delete" class="inline" onsubmit="return confirm('Delete this category and its services?');"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="rounded-md border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-black text-red-700">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-stone-500">No categories yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
