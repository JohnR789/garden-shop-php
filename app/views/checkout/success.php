<!-- Checkout Success Page -->
<div class="alert alert-success mt-4">
    <h2>Thank you for your order, <?= htmlspecialchars($customer_name) ?>!</h2>
    <p>Your order #<?= $orderId ?> has been placed successfully.</p>
    <a href="/" class="btn btn-success">Continue Shopping</a>
</div>
