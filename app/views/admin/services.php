<section class="card">
    <div class="card-header">
        <h3 class="h5 mb-1">Create Service</h3>
        <p class="small text-secondary mb-0">Add a priced option that can be attached to bookings.</p>
    </div>
    <form method="post" action="/admin/services/create" class="card-body row g-3">
        <div class="col-md-3"><select required name="category_id" class="form-select">
            <?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>"><?= $e($category['name']) ?></option><?php endforeach; ?>
        </select></div>
        <div class="col-md-2"><input required name="code" placeholder="code" class="form-control"></div>
        <div class="col-md-3"><input required name="name" placeholder="Name" class="form-control"></div>
        <div class="col-md-2"><input required type="number" step="0.01" min="0" name="price" placeholder="Price" class="form-control"></div>
        <div class="col-md-2"><input type="number" name="sort_order" value="0" class="form-control"></div>
        <div class="col-md-4"><input name="description" placeholder="Description" class="form-control"></div>
        <div class="col-md-3"><input name="unit_label" placeholder="Unit label" class="form-control"></div>
        <div class="col-md-3"><select name="selection_type" class="form-select"><option value="multiple">Multiple</option><option value="single">Single</option><option value="quantity">Quantity</option></select></div>
        <div class="col-md-2 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" checked id="create-service-active"><label class="form-check-label" for="create-service-active">Active</label></div></div>
        <div class="col-12"><button class="btn btn-primary">Create service</button></div>
    </form>
</section>

<section class="card mt-3">
    <div class="card-header"><h3 class="h5 mb-1">Services</h3><p class="small text-secondary mb-0"><?= $e(count($services)) ?> priced booking options</p></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Category</th><th>Code</th><th>Name</th><th>Price</th><th>Type</th><th>Sort</th><th>Active</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><select form="service-<?= $e($service['id']) ?>" name="category_id" class="form-select form-select-sm"><?php foreach ($categories as $category): ?><option value="<?= $e($category['id']) ?>" <?= (int) $service['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= $e($category['name']) ?></option><?php endforeach; ?></select></td>
                        <td><input form="service-<?= $e($service['id']) ?>" name="code" value="<?= $e($service['code']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="service-<?= $e($service['id']) ?>" name="name" value="<?= $e($service['name']) ?>" class="form-control form-control-sm"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="description" value="<?= $e($service['description'] ?? '') ?>"><input form="service-<?= $e($service['id']) ?>" type="hidden" name="unit_label" value="<?= $e($service['unit_label'] ?? '') ?>"></td>
                        <td><input form="service-<?= $e($service['id']) ?>" type="number" step="0.01" min="0" name="price" value="<?= $e($service['price']) ?>" class="form-control form-control-sm"></td>
                        <td><select form="service-<?= $e($service['id']) ?>" name="selection_type" class="form-select form-select-sm"><option value="single" <?= $service['selection_type'] === 'single' ? 'selected' : '' ?>>Single</option><option value="multiple" <?= $service['selection_type'] === 'multiple' ? 'selected' : '' ?>>Multiple</option><option value="quantity" <?= $service['selection_type'] === 'quantity' ? 'selected' : '' ?>>Quantity</option></select></td>
                        <td><input form="service-<?= $e($service['id']) ?>" type="number" name="sort_order" value="<?= $e($service['sort_order']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="service-<?= $e($service['id']) ?>" class="form-check-input" type="checkbox" name="is_active" <?= (int) $service['is_active'] === 1 ? 'checked' : '' ?>></td>
                        <td class="text-nowrap">
                            <form id="service-<?= $e($service['id']) ?>" method="post" action="/admin/services/update" class="d-inline"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="btn btn-sm btn-primary">Save</button></form>
                            <form method="post" action="/admin/services/delete" class="d-inline" onsubmit="return confirm('Delete this service?');"><input type="hidden" name="id" value="<?= $e($service['id']) ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$services): ?>
                    <tr><td colspan="8" class="text-center text-secondary py-4">No services yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
