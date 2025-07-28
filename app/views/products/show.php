<!-- Product detail page -->
<div class="row">
    <div class="col-md-6">
        <img src="<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>
    <div class="col-md-6">
        <h1><?= htmlspecialchars($product['name']) ?></h1>
        <p><?= htmlspecialchars($product['description']) ?></p>
        <h3>$<?= number_format($product['price'], 2) ?></h3>
        <form action="/cart/add" method="post">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <button type="submit" class="btn btn-success btn-lg mt-3">Add to Cart</button>
        </form>
        <a href="/" class="btn btn-outline-secondary mt-2">Back to Shop</a>
    </div>
</div>
