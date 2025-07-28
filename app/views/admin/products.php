<h1>Add New Product</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<!-- Product Add Form -->
<form action="/admin/products/add" method="post" enctype="multipart/form-data" class="mb-5">
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" required></textarea>
    </div>
    <div class="mb-3">
        <label>Price</label>
        <input name="price" type="number" step="0.01" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Image (PNG/JPG, max 5MB)</label>
        <input name="image" type="file" class="form-control" accept=".png,.jpg,.jpeg" required>
    </div>
    <button class="btn btn-success">Add Product</button>
</form>


<!-- Bulk Action Form and Product Table -->
<form action="<?php echo $showDeleted ? '/admin/products/bulk-restore' : '/admin/products/bulk-delete'; ?>" method="post" id="bulk-action-form">
    <h2>
        Current Products
        <!-- Show/Hide deleted toggle -->
        <?php if (!$showDeleted): ?>
            <a href="?show_deleted=1" class="btn btn-link btn-sm">Show Deleted</a>
        <?php else: ?>
            <a href="/admin/products" class="btn btn-link btn-sm">Hide Deleted</a>
        <?php endif; ?>
    </h2>
    <table class="table">
        <thead>
            <tr>
                <!-- Bulk select checkbox -->
                <th><input type="checkbox" id="select-all"></th>
                <th>Image</th>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr class="<?= !empty($p['deleted_at']) ? 'table-secondary' : '' ?>">
                <!-- Allow bulk select for current or deleted based on view -->
                <td>
                    <?php if ($showDeleted && !empty($p['deleted_at'])): ?>
                        <input type="checkbox" name="ids[]" value="<?= $p['id'] ?>">
                    <?php elseif (!$showDeleted && empty($p['deleted_at'])): ?>
                        <input type="checkbox" name="ids[]" value="<?= $p['id'] ?>">
                    <?php endif; ?>
                </td>
                <td><img src="<?= htmlspecialchars($p['image']) ?>" width="80"></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td>$<?= number_format($p['price'], 2) ?></td>
                <td><?= htmlspecialchars($p['description']) ?></td>
                <td>
                    <?php if (!empty($p['deleted_at'])): ?>
                        Deleted
                    <?php else: ?>
                        Active
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (empty($p['deleted_at'])): ?>
                        <!-- Edit and single delete actions -->
                        <a href="/admin/products/edit?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="/admin/products/delete" method="post" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm ms-2">Delete</button>
                        </form>
                    <?php else: ?>
                        <!-- Undo (restore) action for deleted -->
                        <form action="/admin/products/restore" method="post" style="display:inline">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-success btn-sm ms-2">Undo</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($showDeleted): ?>
        <button type="submit" class="btn btn-success" onclick="return confirm('Restore all selected deleted products?')">Restore Selected Deleted</button>
    <?php else: ?>
        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete all selected products?')">Bulk Delete Selected</button>
    <?php endif; ?>
</form>

<!-- Bulk select JS -->
<script>
document.getElementById('select-all').addEventListener('change', function(){
    var cbs = document.querySelectorAll('input[type=checkbox][name="ids[]"]');
    for (var i = 0; i < cbs.length; i++) {
        cbs[i].checked = this.checked;
    }
});
</script>
