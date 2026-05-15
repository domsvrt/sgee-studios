<?php
require_once __DIR__ . '/helpers.php';
/** @var callable $e */
$e = $e ?? static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
/** @var array<int, array<string, mixed>> $services */
$services = $services ?? [];
/** @var array<int, array<string, mixed>> $categories */
$categories = $categories ?? [];
/** @var array<int, array<string, mixed>> $sections */
$sections = $sections ?? [];

$field = 'field';
$fieldSm = 'field field-sm w-full min-w-28';
$selectionTypes = ['multiple', 'single'];
?>
<section class="admin-panel" data-sort-panel>
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Service Sections</h3>
                <p class="admin-panel-subtitle"><?= $e(count($sections)) ?> grouping blocks for category services</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-section-form" data-show-label="Create section" data-hide-label="Hide form">Create section</button>
                <button type="button" class="btn-secondary" data-sort-toggle data-target="sections-sort-form" data-table-id="sections-table">Sort mode</button>
                <button type="submit" form="sections-sort-form" class="btn-primary hidden" data-sort-save>Save order</button>
            </div>
        </div>
        <form id="sections-sort-form" method="post" action="/admin/service-sections/reorder" class="hidden" data-sort-form></form>
    </div>
    <form id="create-section-form" method="post" action="/admin/service-sections/create" class="hidden grid gap-3 p-5 md:grid-cols-4">
        <select required name="category_id" class="<?= $field ?>">
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <input required name="name" placeholder="Section name" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?>">
        <select name="selection_type" class="<?= $field ?>"><?php admin_option_tags($selectionTypes, 'multiple'); ?></select>
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
    </form>
    <div class="overflow-x-auto">
        <table id="sections-table" class="admin-table min-w-[1100px]" data-sortable-table>
            <thead>
                <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Description</th><th class="px-4 py-3">Selection</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody data-sort-body>
                <?php foreach ($sections as $section): ?>
                    <tr data-sort-id="<?= $e($section['id']) ?>">
                        <td class="px-4 py-3 font-black"><?= $e($section['id']) ?></td>
                        <td class="px-4 py-3 text-xs font-black"><?= $e($section['section_code'] ?? '') ?></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="section-<?= $e($section['id']) ?>" name="category_id" class="<?= $fieldSm ?> min-w-44"><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= admin_selected((int) $section['category_id'], (int) $category['id']) ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="section-<?= $e($section['id']) ?>" name="name" value="<?= $e($section['name']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="section-<?= $e($section['id']) ?>" name="description" value="<?= $e($section['description'] ?? '') ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="section-<?= $e($section['id']) ?>" name="selection_type" class="<?= $fieldSm ?>"><?php admin_option_tags($selectionTypes, $section['selection_type']); ?></select></td>
                        <td class="px-4 py-3"><span class="font-black" data-sort-order-display><?= $e($section['sort_order']) ?></span></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="section-<?= $e($section['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-slate-950 dark:text-white" type="checkbox" name="is_active" <?= admin_checked((int) $section['is_active'] === 1) ?>></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                            <form id="section-<?= $e($section['id']) ?>" method="post" action="/admin/service-sections/update"><input type="hidden" name="id" value="<?= $e($section['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                            <form method="post" action="/admin/service-sections/delete" onsubmit="return confirm('Delete this section? Services linked to it will be unassigned.');"><input type="hidden" name="id" value="<?= $e($section['id']) ?>"><button class="btn-danger">Delete</button></form>
                        </div></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$sections): ?>
                    <?php admin_render_empty_row(9, 'No sections yet.'); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<section class="admin-panel mt-5" data-sort-panel>
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Services</h3>
                <p class="admin-panel-subtitle"><?= $e(count($services)) ?> priced booking options</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-service-form" data-show-label="Create service" data-hide-label="Hide form">Create service</button>
                <button type="button" class="btn-secondary" data-sort-toggle data-target="services-sort-form" data-table-id="services-table">Sort mode</button>
                <button type="submit" form="services-sort-form" class="btn-primary hidden" data-sort-save>Save order</button>
            </div>
        </div>
        <form id="services-sort-form" method="post" action="/admin/services/reorder" class="hidden" data-sort-form></form>
    </div>
    <form id="create-service-form" method="post" action="/admin/services/create" class="hidden grid gap-3 p-5 md:grid-cols-4">
        <select required name="category_id" class="<?= $field ?>">
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <select name="section_id" class="<?= $field ?>">
            <option value="">No section</option>
            <?php foreach ($sections as $section): ?><option value="<?= $e($section['id']) ?>"><?= $e($section['category_name'] . ' - ' . $section['name']) ?></option><?php endforeach; ?>
        </select>
        <input required name="code" placeholder="code" class="<?= $field ?>">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input required type="number" step="0.01" min="0" name="price" placeholder="Price" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <input name="unit_label" placeholder="Unit label" class="<?= $field ?>">
        <select name="selection_type" class="<?= $field ?>"><?php admin_option_tags($selectionTypes, 'multiple'); ?></select>
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
    </form>
    <div class="overflow-x-auto">
        <table id="services-table" class="admin-table min-w-[1300px]" data-sortable-table>
            <thead>
                <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Section</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Created At</th><th class="px-4 py-3">Updated At</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody data-sort-body>
                <?php foreach ($services as $service): ?>
                    <tr data-sort-id="<?= $e($service['id']) ?>">
                        <td class="px-4 py-3 font-black"><?= $e($service['id']) ?></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="category_id" class="<?= $fieldSm ?> min-w-44"><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= admin_selected((int) $service['category_id'], (int) $category['id']) ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="section_id" class="<?= $fieldSm ?> min-w-48"><option value="">No section</option><?php foreach ($sections as $section): ?><option value="<?= $e($section['id']) ?>" <?= admin_selected((int) ($service['section_id'] ?? 0), (int) $section['id']) ?>><?= $e($section['category_name'] . ' - ' . $section['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="code" value="<?= $e($service['code']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="name" value="<?= $e($service['name']) ?>" class="<?= $fieldSm ?> min-w-56"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="description" value="<?= $e($service['description'] ?? '') ?>"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="unit_label" value="<?= $e($service['unit_label'] ?? '') ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" type="number" step="0.01" min="0" name="price" value="<?= $e($service['price']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="selection_type" class="<?= $fieldSm ?>"><?php admin_option_tags($selectionTypes, $service['selection_type']); ?></select></td>
                        <td class="px-4 py-3"><span class="font-black" data-sort-order-display><?= $e($service['sort_order']) ?></span></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-slate-950 dark:text-white" type="checkbox" name="is_active" <?= admin_checked((int) $service['is_active'] === 1) ?>><span class="status-badge status-<?= (int) $service['is_active'] === 1 ? 'active' : 'inactive' ?> hidden"><?= (int) $service['is_active'] === 1 ? 'active' : 'inactive' ?></span></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($service['created_at'] ?? '') ?></td>
                        <td class="px-4 py-3 text-xs font-semibold"><?= $e($service['updated_at'] ?? '') ?></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                            <form id="service-<?= $e($service['id']) ?>" method="post" action="/admin/services/update"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                            <form method="post" action="/admin/services/delete" onsubmit="return confirm('Delete this service?');"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="btn-danger">Delete</button></form>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <?php admin_render_empty_row(12, 'No services yet.'); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
