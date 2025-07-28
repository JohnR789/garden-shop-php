<h1>Checkout</h1>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" class="mb-4">
    <div class="mb-3">
        <label>Name</label>
        <input name="name" class="form-control" value="<?= htmlspecialchars($form['name'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($form['email'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" required><?= htmlspecialchars($form['address'] ?? '') ?></textarea>
    </div>
    <h4>Order Summary</h4>
    <table class="table mb-3">
        <thead>
            <tr>
                <th>Tool</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['qty'] ?></td>
                <td>$<?= number_format($item['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>$<?= number_format($total, 2) ?></th>
            </tr>
        </tfoot>
    </table>
    <button class="btn btn-success btn-lg">Place Order</button>
</form>
<a href="/cart" class="btn btn-outline-secondary">Back to Cart</a>

