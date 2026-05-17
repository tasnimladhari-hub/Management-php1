<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo = getPDO();

$stats = [
    'brands'   => $pdo->query('SELECT COUNT(*) FROM brands')->fetchColumn(),
    'products' => $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(),
    'users'    => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
];

$recentBrands   = $pdo->query('SELECT * FROM brands ORDER BY created_at DESC LIMIT 5')->fetchAll();

$recentProducts = $pdo->query(
    'SELECT p.*, b.name AS brand_name
     FROM products p
     JOIN brands b ON p.brand_id = b.id
     ORDER BY p.created_at DESC LIMIT 5'
)->fetchAll();

$flash  = flashGet('success');

$flashE = flashGet('error');

define('PAGE_TITLE', 'Admin Dashboard');

$cssRoot = '../';

include '../includes/header.php';
?>

<div class="admin-layout">

    
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="../images/logo-removebg-preview.png" alt="HERFA">
            <span>HERFA Admin</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="slink active">📊 Dashboard</a>
            <a href="brands.php" class="slink">🏷️ Brands</a>
            <a href="brand_add.php" class="slink">➕ Add Brand</a>
            <a href="products.php" class="slink">🛍️ Products</a>
            <a href="product_add.php" class="slink">➕ Add Product</a>
            <a href="users.php" class="slink">👥 Users</a>
            <hr class="sidebar-hr">
            <a href="../index.php" class="slink">🌐 View Site</a>
            <a href="../logout.php" class="slink slink-logout">🚪 Sign Out</a>
        </nav>
    </aside>

    
    <main class="admin-main">
        <div class="admin-topbar">
            <h2>Good <?= date('H') < 12 ? 'morning' : (date('H') < 18 ? 'afternoon' : 'evening') ?>, <?= htmlspecialchars(currentUser()['username']) ?> 👋</h2>
            <span class="admin-badge">Admin</span>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($flash) ?></div>
        <?php elseif ($flashE): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($flashE) ?></div>
        <?php endif; ?>

        
        <div class="stats-grid">
            <div class="stat-card stat-purple">
                <div class="stat-icon">🏷️</div>
                <div>
                    <div class="stat-num"><?= $stats['brands'] ?></div>
                    <div class="stat-label">Brands</div>
                </div>
            </div>
            <div class="stat-card stat-pink">
                <div class="stat-icon">🛍️</div>
                <div>
                    <div class="stat-num"><?= $stats['products'] ?></div>
                    <div class="stat-label">Products</div>
                </div>
            </div>
            <div class="stat-card stat-teal">
                <div class="stat-icon">👥</div>
                <div>
                    <div class="stat-num"><?= $stats['users'] ?></div>
                    <div class="stat-label">Users</div>
                </div>
            </div>
        </div>

        
        <div class="admin-two-col">
            <div class="admin-panel">
                <div class="panel-header">
                    <h3>Recent Brands</h3>
                    <a href="brands.php" class="panel-link">View all →</a>
                </div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Category</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentBrands as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['name']) ?></td>
                        <td><span class="chip"><?= htmlspecialchars($b['category']) ?></span></td>
                        <td>
                            <a href="brand_edit.php?id=<?= $b['id'] ?>" class="btn-sm btn-edit">Edit</a>
                            <a href="brand_delete.php?id=<?= $b['id'] ?>" class="btn-sm btn-del" onclick="return confirm('Delete this brand?')">Del</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-panel">
                <div class="panel-header">
                    <h3>Recent Products</h3>
                    <a href="products.php" class="panel-link">View all →</a>
                </div>
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Price</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentProducts as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td class="price-cell"><?= number_format($p['price'],2) ?> TND</td>
                        <td>
                            <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn-sm btn-edit">Edit</a>
                            <a href="product_delete.php?id=<?= $p['id'] ?>" class="btn-sm btn-del" onclick="return confirm('Delete?')">Del</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
