<?php

require_once 'includes/config.php';

require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$pdo = getPDO();

$brands = $pdo->query('SELECT * FROM brands ORDER BY created_at DESC')->fetchAll();

$flash = flashGet('success');

define('PAGE_TITLE', 'Artisan Brands');

$cssRoot = '';

include 'includes/header.php';
?>

<header class="hero">
    <img src="images/logo-removebg-preview.png" alt="HERFA Logo" class="logo">
    <h1 class="hero-title">HERFA Artisan Brands</h1>
    <p class="hero-sub">Traditional Tunisian &amp; Handmade Creativity</p>
    <nav class="hero-nav">
        
        <a href="index.php" class="nav-link active">🏠 Home</a>

        
        <a href="products.php" class="nav-link">🛍️ Products</a>
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
        <h2 class="section-title">✨ Discover Our Brands ✨</h2>
        <p class="section-sub">Handpicked artisans preserving Tunisia's rich craft heritage</p>
    </div>

    
    <input type="text" id="search" placeholder="🔎 Search artisan brands..." class="search-box" autocomplete="off">

    <div class="brands-grid" id="brandsGrid">
        <?php

        foreach ($brands as $brand): ?>
        <div class="card"
             data-name="<?= strtolower(htmlspecialchars($brand['name'])) ?>"
             data-cat="<?= strtolower(htmlspecialchars($brand['category'])) ?>">
            <?php

            $img = $brand['image'];
            $src = file_exists($img) ? $img : 'images/potterie.png';
            ?>
            <div class="card-img-wrap">
                <img src="<?= htmlspecialchars($src) ?>"
                     alt="<?= htmlspecialchars($brand['name']) ?>"
                     class="brand-img">
                
                <span class="card-badge"><?= htmlspecialchars($brand['category']) ?></span>
            </div>
            <div class="card-body">
                <h3><?= htmlspecialchars($brand['name']) ?></h3>
                <p><?= htmlspecialchars($brand['description'] ?? '') ?></p>
                
                <a href="products.php?brand_id=<?= $brand['id'] ?>" class="btn-ghost">View Products →</a>
            </div>
        </div>
        <?php endforeach; ?>

        
        <p class="no-results" id="noResults">No brands found. 😢</p>
    </div>
</section>

<script src="script.js"></script>

<?php

include 'includes/footer.php';
?>
