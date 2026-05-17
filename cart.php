<?php

require_once 'includes/config.php';

require_once 'includes/auth.php';

requireLogin();
if (isAdmin()) {

    header('Location: admin/dashboard.php');
    exit;
}

$pdo = getPDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {

    $pid = (int)$_POST['product_id'];

    unset($_SESSION['cart'][$pid]);

    flashSet('success', 'Item removed from cart.');

    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_qty'])) {

    $pid = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];

    if ($qty <= 0) {

        unset($_SESSION['cart'][$pid]);
    } else {

        $_SESSION['cart'][$pid] = $qty;
    }

    flashSet('success', 'Cart updated.');

    header('Location: cart.php');
    exit;
}

$cartItems   = [];
$grandTotal  = 0.0;

if (!empty($_SESSION['cart'])) {

    $ids = array_keys($_SESSION['cart']);

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare(
        "SELECT p.id, p.name, p.price, p.image, b.name AS brand_name
         FROM products p
         JOIN brands b ON p.brand_id = b.id
         WHERE p.id IN ($placeholders)"
    );
    $stmt->execute($ids);

    foreach ($stmt->fetchAll() as $row) {

        $qty = $_SESSION['cart'][$row['id']];

        $subtotal = $row['price'] * $qty;

        $grandTotal += $subtotal;

        $cartItems[] = array_merge($row, ['qty' => $qty, 'subtotal' => $subtotal]);
    }
}

$flash = flashGet('success');

define('PAGE_TITLE', 'My Cart');

$cssRoot = '';

include 'includes/header.php';
?>

<header class="hero">
    <img src="images/logo-removebg-preview.png" alt="HERFA Logo" class="logo">
    <h1 class="hero-title">My Cart 🛒</h1>
    <p class="hero-sub">Review your selected artisan products</p>
    <nav class="hero-nav">
        
        <a href="index.php" class="nav-link">🏠 Home</a>

        
        <a href="products.php" class="nav-link">🛍️ Products</a>

        <?php

        if (!isAdmin()): ?>
            <a href="cart.php" class="nav-link nav-cart active">
                🛒 Cart
                <?php

                $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                ?>
                <span class="cart-badge"><?= $cartCount > 0 ? $cartCount : '' ?></span>
            </a>
        <?php endif; ?>

        <?php

        if (isLoggedIn()): ?>
            <span class="nav-user">👋 <?= htmlspecialchars(currentUser()['username']) ?></span>
            <a href="logout.php" class="nav-link nav-logout">Sign Out</a>
        <?php endif; ?>
    </nav>
</header>

<section class="container">

    <?php

    if ($flash): ?>
        <div class="alert alert-success floating-alert">✅ <?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="section-header">
        <h2 class="section-title">🛒 Your Basket</h2>
        
        <p class="section-sub"><?= count($cartItems) ?> item type<?= count($cartItems) !== 1 ? 's' : '' ?> in your cart</p>
    </div>

    <?php

    if ($cartItems): ?>
    <div class="cart-table-wrap">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                foreach ($cartItems as $item): ?>
                <tr>
                    <td class="cart-product-cell">
                        <?php

                        $img = $item['image'] ?? '';
                        ?>
                        <img src="<?= $img && file_exists($img) ? htmlspecialchars($img) : 'images/oil.png' ?>"
                             alt="<?= htmlspecialchars($item['name']) ?>"
                             class="cart-thumb">
                        
                        <?= htmlspecialchars($item['name']) ?>
                    </td>
                    <td><?= htmlspecialchars($item['brand_name']) ?></td>
                    
                    <td><?= number_format($item['price'], 2) ?> TND</td>
                    <td>
                        
                        <form method="POST" action="cart.php" style="display:flex;gap:6px;align-items:center">
                            
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            
                            <input type="number" name="quantity" value="<?= $item['qty'] ?>"
                                   min="0" max="99" class="qty-input">
                            
                            <input type="hidden" name="update_qty" value="1">
                            <button type="submit" class="btn-qty-update" title="Update quantity">↺</button>
                        </form>
                    </td>
                    
                    <td class="subtotal-cell"><?= number_format($item['subtotal'], 2) ?> TND</td>
                    <td>
                        
                        <form method="POST" action="cart.php">
                            
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="remove_item" value="1">
                            <button type="submit" class="btn-remove" title="Remove item">✕</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="grand-total-label">Grand Total</td>
                    
                    <td class="grand-total-value" colspan="2"><?= number_format($grandTotal, 2) ?> TND</td>
                </tr>
            </tfoot>
        </table>
    </div>

    
    <div class="cart-actions">
        
        <a href="products.php" class="btn-ghost">← Continue Shopping</a>
        
        <button class="btn-primary" onclick="alert('Checkout coming soon! 🎉')">Checkout →</button>
    </div>

    <?php else: ?>
        
        <div class="empty-state">
            <p>😢 Your cart is empty.</p>
            
            <a href="products.php" class="btn-primary" style="display:inline-block;margin-top:1rem">Browse Products →</a>
        </div>
    <?php endif; ?>
</section>

<?php

include 'includes/footer.php';
?>
