<?php
$field = 'field';
$fieldSm = 'field field-sm w-full min-w-28';
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Create Category</h3>
                <p class="admin-panel-subtitle">Organize services into booking groups.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-category-form" data-show-label="Create category" data-hide-label="Hide form">Create category</button>
                <button type="submit" form="create-category-form" class="btn-primary hidden" data-create-submit="create-category-form">Create category</button>
            </div>
        </div>
    </div>
    <form id="create-category-form" method="post" action="/admin/categories/create" class="hidden grid gap-3 p-5 md:grid-cols-4">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <input type="number" name="sort_order" value="0" class="<?= $field ?>">
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
    </form>
</section>

<section class="admin-panel mt-5">
    <div class="admin-panel-header"><h3 class="admin-panel-title">Categories</h3><p class="admin-panel-subtitle"><?= $e(count($categories)) ?> service groups</p></div>
    <div class="overflow-x-auto">
        <table class="admin-table min-w-[900px]">
            <thead>
                <tr><th class="px-4 py-3">Name</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" name="name" value="<?= $e($category['name']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" name="description" value="<?= $e($category['description'] ?? '') ?>" class="<?= $fieldSm ?> min-w-72"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" type="number" name="sort_order" value="<?= $e($category['sort_order']) ?>" class="<?= $fieldSm ?> max-w-24"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-slate-950 dark:text-white" type="checkbox" name="is_active" <?= (int) $category['is_active'] === 1 ? 'checked' : '' ?>><span class="status-badge status-<?= (int) $category['is_active'] === 1 ? 'active' : 'inactive' ?> hidden"><?= (int) $category['is_active'] === 1 ? 'active' : 'inactive' ?></span></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                            <form id="category-<?= $e($category['id']) ?>" method="post" action="/admin/categories/update"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                            <form method="post" action="/admin/categories/delete" onsubmit="return confirm('Delete this category and its services?');"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="btn-danger">Delete</button></form>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No categories yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
