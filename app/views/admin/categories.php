<?php
require_once __DIR__ . '/helpers.php';
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array<int, array<string, mixed>> $categories */
$categories = $categories ?? [];

$field = 'field';
$fieldSm = 'field field-sm w-full min-w-28';
?>
<section class="admin-panel" data-sort-panel>
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Categories</h3>
                <p class="admin-panel-subtitle"><?= $e(count($categories)) ?> service groups</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-category-form" data-show-label="Create category" data-hide-label="Hide form">Create category</button>
                <button type="button" class="btn-secondary" data-sort-toggle data-target="categories-sort-form" data-table-id="categories-table">Sort mode</button>
                <button type="submit" form="categories-sort-form" class="btn-primary hidden" data-sort-save>Save order</button>
            </div>
        </div>
        <form id="categories-sort-form" method="post" action="/admin/categories/reorder" class="hidden" data-sort-form></form>
    </div>
    <form id="create-category-form" method="post" action="/admin/categories/create" class="hidden grid gap-3 p-5 md:grid-cols-4">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
    </form>
    <div class="overflow-x-auto">
        <table id="categories-table" class="admin-table min-w-[900px]" data-sortable-table>
            <thead>
                <tr><th class="px-4 py-3">Code</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Created At</th><th class="px-4 py-3">Updated At</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody data-sort-body>
                <?php foreach ($categories as $category): ?>
                    <tr data-sort-id="<?= $e($category['id']) ?>">
                        <td class="px-4 py-3 text-xs font-black"><?= $e($category['category_code'] ?? '') ?></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" name="name" value="<?= $e($category['name']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" name="description" value="<?= $e($category['description'] ?? '') ?>" class="<?= $fieldSm ?> min-w-72"></td>
                        <td class="px-4 py-3"><span class="font-black" data-sort-order-display><?= $e($category['sort_order']) ?></span></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="category-<?= $e($category['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-slate-950 dark:text-white" type="checkbox" name="is_active" <?= admin_checked((int) $category['is_active'] === 1) ?>><span class="status-badge status-<?= (int) $category['is_active'] === 1 ? 'active' : 'inactive' ?> hidden"><?= (int) $category['is_active'] === 1 ? 'active' : 'inactive' ?></span></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($category['created_at'] ?? '') ?></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($category['updated_at'] ?? '') ?></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                            <form id="category-<?= $e($category['id']) ?>" method="post" action="/admin/categories/update"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                            <form method="post" action="/admin/categories/delete" onsubmit="return confirm('Delete this category and its services?');"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="btn-danger">Delete</button></form>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <?php admin_render_empty_row(8, 'No categories yet.'); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
