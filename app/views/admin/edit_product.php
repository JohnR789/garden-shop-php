<h1>Edit Product</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form action="/admin/products/update" method="post" enctype="multipart/form-data" class="mb-4">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control" required><?= htmlspecialchars($product['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Price</label>
        <input name="price" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($product['price']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label>Current Image</label><br>
        <img src="<?= htmlspecialchars($product['image']) ?>" width="120"><br>
        <label class="mt-2">Change Image (optional)</label>
        <input name="image" type="file" class="form-control" accept=".png,.jpg,.jpeg">
    </div>
    <button class="btn btn-success">Update Product</button>
    <a href="/admin/products" class="btn btn-outline-secondary ms-2">Cancel</a>
</form>

