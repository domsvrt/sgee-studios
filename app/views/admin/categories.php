<section class="card">
    <div class="card-header">
        <h3 class="h5 mb-1">Create Category</h3>
        <p class="small text-secondary mb-0">Organize services into booking groups.</p>
    </div>
    <form method="post" action="/admin/categories/create" class="card-body row g-3">
        <div class="col-md-2"><input required name="slug" placeholder="slug" class="form-control"></div>
        <div class="col-md-3"><input required name="name" placeholder="Name" class="form-control"></div>
        <div class="col-md-4"><input name="description" placeholder="Description" class="form-control"></div>
        <div class="col-md-1"><input type="number" name="sort_order" value="0" class="form-control"></div>
        <div class="col-md-2 d-flex align-items-center"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" checked id="create-category-active"><label class="form-check-label" for="create-category-active">Active</label></div></div>
        <div class="col-12"><button class="btn btn-primary">Create category</button></div>
    </form>
</section>

<section class="card mt-3">
    <div class="card-header"><h3 class="h5 mb-1">Categories</h3><p class="small text-secondary mb-0"><?= $e(count($categories)) ?> service groups</p></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Slug</th><th>Name</th><th>Description</th><th>Sort</th><th>Active</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><input form="category-<?= $e($category['id']) ?>" name="slug" value="<?= $e($category['slug']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="category-<?= $e($category['id']) ?>" name="name" value="<?= $e($category['name']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="category-<?= $e($category['id']) ?>" name="description" value="<?= $e($category['description'] ?? '') ?>" class="form-control form-control-sm"></td>
                        <td><input form="category-<?= $e($category['id']) ?>" type="number" name="sort_order" value="<?= $e($category['sort_order']) ?>" class="form-control form-control-sm"></td>
                        <td><input form="category-<?= $e($category['id']) ?>" class="form-check-input" type="checkbox" name="is_active" <?= (int) $category['is_active'] === 1 ? 'checked' : '' ?>></td>
                        <td class="text-nowrap">
                            <form id="category-<?= $e($category['id']) ?>" method="post" action="/admin/categories/update" class="d-inline"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="btn btn-sm btn-primary">Save</button></form>
                            <form method="post" action="/admin/categories/delete" class="d-inline" onsubmit="return confirm('Delete this category and its services?');"><input type="hidden" name="id" value="<?= $e($category['id']) ?>"><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$categories): ?>
                    <tr><td colspan="6" class="text-center text-secondary py-4">No categories yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
