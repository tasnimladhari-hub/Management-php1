<?php

require_once 'includes/config.php';

require_once 'includes/auth.php';

requireLogin();

$pdo = getPDO();

$brandId = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart']) && !isAdmin()) {

    $pid = (int)$_POST['product_id'];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + 1;

    flashSet('success', 'Item added to cart! 🛒');

    $redirect = $brandId ? "products.php?brand_id=$brandId" : "products.php";
    header("Location: $redirect");
    exit;
}

if ($brandId) {

    $stmt = $pdo->prepare(
        'SELECT p.*, b.name AS brand_name
         FROM products p
         JOIN brands b ON p.brand_id = b.id
         WHERE p.brand_id = ?
         ORDER BY p.created_at DESC'
    );
    $stmt->execute([$brandId]);

    $brandRow = $pdo->prepare('SELECT name FROM brands WHERE id = ?');
    $brandRow->execute([$brandId]);
    $brandName = $brandRow->fetchColumn() ?: 'All Products';
} else {

    $stmt = $pdo->query(
        'SELECT p.*, b.name AS brand_name
         FROM products p
         JOIN brands b ON p.brand_id = b.id
         ORDER BY p.created_at DESC'
    );

    $brandName = 'All Products';
}

$products = $stmt->fetchAll();

$brands = $pdo->query('SELECT id, name FROM brands ORDER BY name')->fetchAll();

$flash = flashGet('success');

define('PAGE_TITLE', 'Products');

$cssRoot = '';

include 'includes/header.php';
?>

<header class="hero">
    <img src="images/logo-removebg-preview.png" alt="HERFA Logo" class="logo">
    <h1 class="hero-title">HERFA Products</h1>
    <p class="hero-sub">Authentic handmade goods from Tunisian artisans</p>
    <nav class="hero-nav">
        
        <a href="index.php" class="nav-link">🏠 Home</a>

        
        <a href="products.php" class="nav-link active">🛍️ Products</a>
        <a href="about.php"   class="nav-link">🌿 About</a>
        <a href="contact.php" class="nav-link">📬 Contact</a>

        <?php

        if (isAdmin()): ?>
            <a href="admin/dashboard.php" class="nav-link nav-admin">⚡ Admin</a>
        <?php endif; ?>

        <?php

        if (isLoggedIn()): ?>
        <?php

            if (!isAdmin()): ?>
                <a href="cart.php" class="nav-link nav-cart" id="cartNavLink">
                    🛒 Cart
                    <?php

                    $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
                    ?>
                    
                    <span class="cart-badge" id="cartBadge"><?= $cartCount > 0 ? $cartCount : '' ?></span>
                </a>
            <?php endif; ?>

            <a href="logout.php" class="nav-link nav-logout">Sign Out</a>
        <?php else: ?>
            
            <a href="login.php" class="nav-link">Sign In</a>
        <?php endif; ?>
    </nav>
</header>

<section class="container">

    <?php

    if ($flash): ?>
        <div class="alert alert-success floating-alert">✅ <?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="section-header">
        
        <h2 class="section-title">🛍️ <?= htmlspecialchars($brandName) ?></h2>
        
        <p class="section-sub"><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> available</p>
    </div>

    
    <div class="filter-bar">
        
        <a href="products.php" class="filter-chip <?= !$brandId ? 'active' : '' ?>">All</a>
        <?php

        foreach ($brands as $b): ?>
            <a href="products.php?brand_id=<?= $b['id'] ?>"
               class="filter-chip <?= $brandId == $b['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($b['name']) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php

    if ($products): ?>
    <div class="products-grid">
        <?php

        foreach ($products as $p): ?>
        <div class="product-card">
            <?php

            $img = $p['image'] ?? '';
            ?>
            <div class="product-img-wrap">
                <img src="<?= $img ? htmlspecialchars($img) : 'images/oil.png' ?>">
                     alt="<?= htmlspecialchars($p['name']) ?>">
            </div>
            <div class="product-body">
                
                <span class="product-brand"><?= htmlspecialchars($p['brand_name']) ?></span>
                <h3><?= htmlspecialchars($p['name']) ?></h3>
                <p><?= htmlspecialchars($p['description'] ?? '') ?></p>
                <div class="product-footer">
                    
                    <span class="price"><?= number_format($p['price'], 2) ?> TND</span>

                    <?php

                    if (!isAdmin()): ?>
                        
                        <form method="POST" action="products.php<?= $brandId ? '?brand_id='.$brandId : '' ?>" style="display:inline">
                            
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            
                            <input type="hidden" name="add_to_cart" value="1">
                            <button type="submit" class="btn-add-cart">🛒 Add to Cart</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        
        <p class="empty-state">😢 No products found for this brand yet.</p>
    <?php endif; ?>
</section>

<?php

include 'includes/footer.php';
?>
