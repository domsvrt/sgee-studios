<?php
$field = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-950 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-teal-600 focus:ring-4 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100 dark:placeholder:text-slate-500';
$fieldSm = 'w-full min-w-28 rounded-md border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-950 shadow-sm outline-none transition focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-100';
?>
<section class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 bg-slate-50/70 px-5 py-4 dark:border-slate-800 dark:bg-slate-950/40">
        <h3 class="text-base font-black text-slate-950 dark:text-white">Create Service</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Add a priced option that can be attached to bookings.</p>
    </div>
    <form method="post" action="/admin/services/create" class="grid gap-3 p-5 md:grid-cols-4">
        <select required name="category_id" class="<?= $field ?>">
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select>
        <input required name="code" placeholder="code" class="<?= $field ?>">
        <input required name="name" placeholder="Name" class="<?= $field ?>">
        <input required type="number" step="0.01" min="0" name="price" placeholder="Price" class="<?= $field ?>">
        <input name="description" placeholder="Description" class="<?= $field ?> md:col-span-2">
        <input name="unit_label" placeholder="Unit label" class="<?= $field ?>">
        <select name="selection_type" class="<?= $field ?>"><option value="multiple">Multiple</option><option value="single">Single</option><option value="quantity">Quantity</option></select>
        <input type="number" name="sort_order" value="0" class="<?= $field ?>">
        <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200"><input type="checkbox" name="is_active" checked class="h-4 w-4 rounded border-slate-300 text-teal-600"> Active</label>
        <button class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-bold text-white shadow-sm transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 md:col-span-2">Create service</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-panel dark:border-slate-800 dark:bg-slate-900">
    <div class="border-b border-slate-200 px-5 py-4 dark:border-slate-800"><h3 class="text-base font-black text-slate-950 dark:text-white">Services</h3><p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><?= $e(count($services)) ?> priced booking options</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px] text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 dark:bg-slate-950/50 dark:text-slate-400">
                <tr><th class="px-4 py-3">Category</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <?php foreach ($services as $service): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-4 py-3"><select form="service-<?= $e($service['id']) ?>" name="category_id" class="<?= $fieldSm ?> min-w-44"><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) $service['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" name="code" value="<?= $e($service['code']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" name="name" value="<?= $e($service['name']) ?>" class="<?= $fieldSm ?> min-w-56"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="description" value="<?= $e($service['description'] ?? '') ?>"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="unit_label" value="<?= $e($service['unit_label'] ?? '') ?>"></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" type="number" step="0.01" min="0" name="price" value="<?= $e($service['price']) ?>" class="<?= $fieldSm ?>"></td>
                        <td class="px-4 py-3"><select form="service-<?= $e($service['id']) ?>" name="selection_type" class="<?= $fieldSm ?>"><option value="single" <?= $service['selection_type'] === 'single' ? 'selected' : '' ?>>Single</option><option value="multiple" <?= $service['selection_type'] === 'multiple' ? 'selected' : '' ?>>Multiple</option><option value="quantity" <?= $service['selection_type'] === 'quantity' ? 'selected' : '' ?>>Quantity</option></select></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" type="number" name="sort_order" value="<?= $e($service['sort_order']) ?>" class="<?= $fieldSm ?> max-w-24"></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" class="h-4 w-4 rounded border-slate-300 text-teal-600" type="checkbox" name="is_active" <?= (int) $service['is_active'] === 1 ? 'checked' : '' ?>></td>
                        <td class="px-4 py-3"><div class="flex gap-2">
                            <form id="service-<?= $e($service['id']) ?>" method="post" action="/admin/services/update"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="rounded-lg bg-slate-950 px-3 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-slate-800 dark:bg-white dark:text-slate-950">Save</button></form>
                            <form method="post" action="/admin/services/delete" onsubmit="return confirm('Delete this service?');"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="rounded-lg border border-rose-300 bg-white px-3 py-1.5 text-xs font-bold text-rose-700 shadow-sm transition hover:bg-rose-50 dark:border-rose-900 dark:bg-slate-900 dark:text-rose-300 dark:hover:bg-rose-950/40">Delete</button></form>
                        </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-slate-500 dark:text-slate-400">No services yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
