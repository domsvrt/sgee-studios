<?php
$field = 'field';
$fieldSm = 'field field-sm w-full min-w-28';
?>
<section class="admin-panel">
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Create Service</h3>
                <p class="admin-panel-subtitle">Add a priced option that can be attached to bookings.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-create-toggle data-target="create-service-form" data-show-label="Create service" data-hide-label="Hide form">Create service</button>
                <button type="submit" form="create-service-form" class="btn-primary hidden" data-create-submit="create-service-form">Create service</button>
            </div>
        </div>
    </div>
    <form id="create-service-form" method="post" action="/admin/services/create" class="hidden grid gap-3 p-5 md:grid-cols-4">
        <select required name="category_id" class="<?= $field ?>">
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <input required name="code" placeholder="code" class="<?= $field ?>">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input required type="number" step="0.01" min="0" name="price" placeholder="Price" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <input name="unit_label" placeholder="Unit label" class="<?= $field ?>">
        <select name="selection_type" class="<?= $field ?>"><option value="multiple">Multiple</option><option value="single">Single</option><option value="quantity">Quantity</option></select>
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
    </form>
</section>

<section class="admin-panel mt-5" data-sort-panel>
    <div class="admin-panel-header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="admin-panel-title">Services</h3>
                <p class="admin-panel-subtitle"><?= $e(count($services)) ?> priced booking options</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary" data-sort-toggle data-target="services-sort-form" data-table-id="services-table">Sort mode</button>
                <button type="submit" form="services-sort-form" class="btn-primary hidden" data-sort-save>Save order</button>
            </div>
        </div>
        <form id="services-sort-form" method="post" action="/admin/services/reorder" class="hidden" data-sort-form></form>
    </div>
    <div class="overflow-x-auto">
        <table id="services-table" class="admin-table min-w-[1200px]" data-sortable-table>
            <thead>
                <tr><th class="px-4 py-3">ID</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody data-sort-body>
                <?php foreach ($services as $service): ?>
                    <tr data-sort-id="<?= $e($service['id']) ?>">
                        <td class="px-4 py-3 font-black"><?= $e($service['id']) ?></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="category_id" class="<?= $fieldSm ?> min-w-44"><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) $service['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="code" value="<?= $e($service['code']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="name" value="<?= $e($service['name']) ?>" class="<?= $fieldSm ?> min-w-56"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="description" value="<?= $e($service['description'] ?? '') ?>"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="unit_label" value="<?= $e($service['unit_label'] ?? '') ?>"></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" type="number" step="0.01" min="0" name="price" value="<?= $e($service['price']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select data-edit-field disabled form="service-<?= $e($service['id']) ?>" name="selection_type" class="<?= $fieldSm ?>"><option value="single" <?= $service['selection_type'] === 'single' ? 'selected' : '' ?>>Single</option><option value="multiple" <?= $service['selection_type'] === 'multiple' ? 'selected' : '' ?>>Multiple</option><option value="quantity" <?= $service['selection_type'] === 'quantity' ? 'selected' : '' ?>>Quantity</option></select></td>
                        <td class="px-4 py-3"><span class="font-black" data-sort-order-display><?= $e($service['sort_order']) ?></span></td>
                        <td class="px-4 py-3"><input data-edit-field disabled form="service-<?= $e($service['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-slate-950 dark:text-white" type="checkbox" name="is_active" <?= (int) $service['is_active'] === 1 ? 'checked' : '' ?>><span class="status-badge status-<?= (int) $service['is_active'] === 1 ? 'active' : 'inactive' ?> hidden"><?= (int) $service['is_active'] === 1 ? 'active' : 'inactive' ?></span></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <button type="button" data-edit-button class="btn-secondary min-h-8 px-3 py-1.5 text-xs">Edit</button>
                            <form id="service-<?= $e($service['id']) ?>" method="post" action="/admin/services/update"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button data-save-button class="btn-primary hidden min-h-8 px-3 py-1.5 text-xs">Save</button></form>
                            <form method="post" action="/admin/services/delete" onsubmit="return confirm('Delete this service?');"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="btn-danger">Delete</button></form>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <tr><td colspan="9" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No services yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
