<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Garden Tools Shop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS for quick, responsive design -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
      #mainHeader {
        transition: transform 0.3s;
        z-index: 1030;
        will-change: transform;
      }
      @media (max-width: 767px) {
        #mainHeader { transform: translateY(0) !important; }
      }
    </style>
</head>
<body style="background: #f7faf7;">
<nav id="mainHeader" class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/" style="font-family: 'Inter', Arial, sans-serif; font-size:2rem;">🌱 Garden Tools Shop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarsExample07">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link position-relative" href="/cart" id="cart-link">
                        Cart
                        <?php
                            // Use DB cart count for logged-in, session cart for guests
                            if (!empty($_SESSION['user_id'])) {
                                if (!class_exists('\App\Models\Cart')) {
                                    require_once __DIR__ . '/../../models/Cart.php';
                                }
                                $cartId = \App\Models\Cart::getActiveCartId($_SESSION['user_id']);
                                $cart_count = $cartId ? \App\Models\Cart::countItems($cartId) : 0;
                            } else {
                                $cart_count = array_sum($_SESSION['cart'] ?? []);
                            }
                            $badgeClass = $cart_count > 0 ? 'bg-warning text-dark' : 'bg-secondary';
                            $badgeStyle = 'font-size:0.9em;' . ($cart_count == 0 ? 'opacity:0.4;' : '');
                        ?>
                        <span
                            id="cart-badge"
                            class="badge ms-1 <?= $badgeClass ?>"
                            style="<?= $badgeStyle ?>"
                        ><?= $cart_count ?></span>
                    </a>
                </li>
                <?php if (!empty($_SESSION['user'])): ?>
                    <!-- Show Admin link if this user is an admin -->
                    <?php
                        // Cache is_admin in session for performance
                        if (!isset($_SESSION['is_admin'])) {
                            $user = \App\Models\User::findByUsername($_SESSION['user']);
                            $_SESSION['is_admin'] = !empty($user['is_admin']);
                        }
                        if ($_SESSION['is_admin']) {
                            echo '<li class="nav-item"><a class="nav-link fw-bold text-warning" href="/admin/products">Admin</a></li>';
                        }
                    ?>
                    <li class="nav-item"><a class="nav-link" href="#">Hi, <?= htmlspecialchars($_SESSION['user']) ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/login">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">








