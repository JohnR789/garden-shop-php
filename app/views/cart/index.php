<h1>Your Cart</h1>
<?php if (empty($products)): ?>
    <div class="alert alert-info mt-4">
        Your cart is empty. <a href="/">Browse products</a>.
    </div>
<?php else: ?>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>Tool</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td><?= $item['qty'] ?></td>
                <td>$<?= number_format($item['subtotal'], 2) ?></td>
                <td>
                    <form action="/cart/remove" method="post" style="display:inline">
                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger remove-from-cart-btn">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-end">Total:</th>
                <th>$<?= number_format($total, 2) ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    <a href="/" class="btn btn-outline-secondary">Continue Shopping</a>
    <a href="/checkout" class="btn btn-primary btn-lg mt-3">Proceed to Checkout</a>
<?php endif; ?>


