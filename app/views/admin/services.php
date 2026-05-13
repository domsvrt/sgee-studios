<?php $field = 'rounded-md border border-stone-300 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-clay focus:ring-2 focus:ring-clay/20'; ?>
<section class="overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 bg-stone-50/70 px-5 py-4">
        <h3 class="text-lg font-black">Create Service</h3>
        <p class="mt-1 text-sm text-stone-500">Add a priced option that can be attached to bookings.</p>
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
        <label class="flex items-center gap-2 rounded-md border border-stone-200 bg-stone-50 px-3 py-2 text-sm font-semibold"><input type="checkbox" name="is_active" checked> Active</label>
        <button class="rounded-md bg-ink px-4 py-2 text-sm font-black text-white shadow-sm md:col-span-2">Create service</button>
    </form>
</section>

<section class="mt-5 overflow-hidden rounded-md border border-stone-200 bg-white shadow-soft">
    <div class="border-b border-stone-200 px-5 py-4"><h3 class="text-lg font-black">Services</h3><p class="mt-1 text-sm text-stone-500"><?= $e(count($services)) ?> priced booking options</p></div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1200px] text-left text-sm">
            <thead class="bg-stone-50 text-xs uppercase text-stone-500">
                <tr><th class="px-4 py-3">Category</th><th class="px-4 py-3">Code</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Price</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Sort</th><th class="px-4 py-3">Active</th><th class="px-4 py-3">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                <?php foreach ($services as $service): ?>
                    <tr class="hover:bg-stone-50">
                        <td class="px-4 py-3"><select form="service-<?= $e($service['id']) ?>" name="category_id" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) $service['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" name="code" value="<?= $e($service['code']) ?>" class="w-28 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" name="name" value="<?= $e($service['name']) ?>" class="w-full min-w-56 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="description" value="<?= $e($service['description'] ?? '') ?>"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="unit_label" value="<?= $e($service['unit_label'] ?? '') ?>"></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" type="number" step="0.01" min="0" name="price" value="<?= $e($service['price']) ?>" class="w-28 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><select form="service-<?= $e($service['id']) ?>" name="selection_type" class="rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"><option value="single" <?= $service['selection_type'] === 'single' ? 'selected' : '' ?>>Single</option><option value="multiple" <?= $service['selection_type'] === 'multiple' ? 'selected' : '' ?>>Multiple</option><option value="quantity" <?= $service['selection_type'] === 'quantity' ? 'selected' : '' ?>>Quantity</option></select></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" type="number" name="sort_order" value="<?= $e($service['sort_order']) ?>" class="w-20 rounded-md border border-stone-300 px-2 py-1.5 shadow-sm"></td>
                        <td class="px-4 py-3"><input form="service-<?= $e($service['id']) ?>" type="checkbox" name="is_active" <?= (int) $service['is_active'] === 1 ? 'checked' : '' ?>></td>
                        <td class="px-4 py-3">
                            <form id="service-<?= $e($service['id']) ?>" method="post" action="/admin/services/update" class="inline"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="rounded-md bg-ink px-3 py-1.5 text-xs font-black text-white">Save</button></form>
                            <form method="post" action="/admin/services/delete" class="inline" onsubmit="return confirm('Delete this service?');"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="rounded-md border border-red-300 bg-red-50 px-3 py-1.5 text-xs font-black text-red-700">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <tr><td colspan="8" class="px-5 py-12 text-center text-stone-500">No services yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
