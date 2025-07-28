<h1 class="mb-4">All Gardening Tools</h1>

<!-- Search, Category Filter, and Sort Form -->
<form method="get" class="row g-2 align-items-end mb-4" autocomplete="off" id="search-form">
    <div class="col-md-3 position-relative">
        <input type="text" id="live-search" name="search" placeholder="Search tools..." value="<?= htmlspecialchars($search_query ?? '') ?>" class="form-control" autocomplete="off" aria-label="Search tools">
        <div id="search-results" class="list-group position-absolute w-100" style="z-index: 1000;"></div>
    </div>
    <div class="col-md-3">
        <select name="category" class="form-select" onchange="this.form.submit()" aria-label="Filter by category">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($selected_category == $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="sort" class="form-select" onchange="this.form.submit()" aria-label="Sort products">
            <option value="newest" <?= ($sort === 'newest') ? 'selected' : '' ?>>Newest</option>
            <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name_asc" <?= ($sort === 'name_asc') ? 'selected' : '' ?>>Name: A-Z</option>
            <option value="name_desc" <?= ($sort === 'name_desc') ? 'selected' : '' ?>>Name: Z-A</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-success w-100">Search</button>
    </div>
</form>

<div class="row">
    <?php foreach ($products as $product): ?>
    <div class="col-md-4 mb-4 position-relative">
        <div class="card h-100 shadow" tabindex="0" aria-label="<?= htmlspecialchars($product['name']) ?>">
            <img src="<?= htmlspecialchars($product['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                <?php
                    $cat_name = '';
                    foreach ($categories as $cat) {
                        if ($cat['id'] == ($product['category_id'] ?? null)) {
                            $cat_name = $cat['name'];
                            break;
                        }
                    }
                ?>
                <?php if ($cat_name): ?>
                    <span class="badge bg-secondary mb-2"><?= htmlspecialchars($cat_name) ?></span>
                <?php endif; ?>
                <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                <p class="card-text"><strong>$<?= number_format($product['price'], 2) ?></strong></p>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center bg-white border-0" style="position:relative;">
                <a href="/product?id=<?= $product['id'] ?>" class="btn btn-outline-success btn-sm">View Details</a>
                <!-- AJAX Add to Cart -->
                <form action="/cart/add" method="post" class="d-inline add-to-cart-form" style="position:absolute; right:1rem; bottom:1rem;" data-product-name="<?= htmlspecialchars($product['name']) ?>">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn btn-success btn-sm">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pagination Navigation with Next/Previous -->
<?php if ($totalPages > 1): ?>
<nav aria-label="Product pages">
    <ul class="pagination justify-content-center mt-4">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link"
               href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])) ?>"
               tabindex="-1"
               aria-disabled="<?= ($page <= 1) ? 'true' : 'false' ?>">
                &laquo; Previous
            </a>
        </li>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                <a class="page-link"
                   href="?<?= http_build_query(array_merge($_GET, ['page' => $p])) ?>">
                    <?= $p ?>
                </a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
            <a class="page-link"
               href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)])) ?>"
               aria-disabled="<?= ($page >= $totalPages) ? 'true' : 'false' ?>">
                Next &raquo;
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- Live Search Script & AJAX Cart Badge Update -->
<script>
const searchInput = document.getElementById('live-search');
const resultsDiv = document.getElementById('search-results');
searchInput.addEventListener('input', function() {
    const query = this.value.trim();
    if (query.length === 0) {
        resultsDiv.innerHTML = '';
        return;
    }
    fetch('/api/search-products.php?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(products => {
            if (!products.length) {
                resultsDiv.innerHTML = '';
                return;
            }
            resultsDiv.innerHTML = products.map(p => `
                <a href="/product?id=${p.id}" class="list-group-item list-group-item-action d-flex align-items-center">
                    <img src="${p.image}" width="36" height="36" class="me-2 rounded" alt="${p.name}">
                    <span>${p.name} <span class="text-muted ms-2 small">$${Number(p.price).toFixed(2)}</span></span>
                </a>
            `).join('');
        });
});
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
        resultsDiv.innerHTML = '';
    }
});

// AJAX Add-to-cart for toast and cart badge update
document.querySelectorAll('.add-to-cart-form').forEach(form => {
  form.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(form);
    fetch('/cart/add', {
      method: 'POST',
      body: formData,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(resp => resp.json())
    .then(data => {
      showToast(data.message || 'Added to cart!', data.success ? 'bg-success' : 'bg-danger');
      // Update the cart badge if server responds with count
      if (typeof data.cart_count !== 'undefined') {
        const badge = document.getElementById('cart-badge');
        if (badge) badge.textContent = data.cart_count;
      } else {
        // fallback: increment by one (may not be perfectly accurate with multi-item forms, but works for typical "add one" UX)
        const badge = document.getElementById('cart-badge');
        if (badge && data.success) badge.textContent = parseInt(badge.textContent || "0", 10) + 1;
      }
    })
    .catch(() => {
      showToast('Could not add to cart.', 'bg-danger');
    });
  });
});
</script>

