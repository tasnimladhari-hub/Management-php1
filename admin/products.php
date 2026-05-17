<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo      = getPDO();
$products = $pdo->query('SELECT p.*, b.name AS brand_name FROM products p JOIN brands b ON p.brand_id=b.id ORDER BY p.created_at DESC')->fetchAll();
$flash    = flashGet('success');
$flashE   = flashGet('error');

define('PAGE_TITLE', 'Manage Products');
$cssRoot = '../';
include '../includes/header.php';
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo"><img src="../images/logo-removebg-preview.png" alt="HERFA"><span>HERFA Admin</span></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="slink">📊 Dashboard</a>
            <a href="brands.php" class="slink">🏷️ Brands</a>
            <a href="brand_add.php" class="slink">➕ Add Brand</a>
            <a href="products.php" class="slink active">🛍️ Products</a>
            <a href="product_add.php" class="slink">➕ Add Product</a>
            <a href="users.php" class="slink">👥 Users</a>
            <hr class="sidebar-hr">
            <a href="../index.php" class="slink">🌐 View Site</a>
            <a href="../logout.php" class="slink slink-logout">🚪 Sign Out</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-topbar">
            <h2>🛍️ All Products</h2>
            <a href="product_add.php" class="btn-primary">➕ Add Product</a>
        </div>
        <?php if ($flash): ?><div class="alert alert-success">✅ <?= htmlspecialchars($flash) ?></div><?php endif; ?>
        <?php if ($flashE): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($flashE) ?></div><?php endif; ?>

        <div class="admin-panel">
            <table class="admin-table">
                <thead><tr><th>#</th><th>Img</th><th>Name</th><th>Brand</th><th>Price</th><th>Added</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?php $img = $p['image'] ?? ''; ?>
                        <img src="../<?= $img && file_exists('../'.$img) ? htmlspecialchars($img) : 'images/oil.png' ?>" class="thumb" alt="">
                    </td>
                    <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                    <td><span class="chip"><?= htmlspecialchars($p['brand_name']) ?></span></td>
                    <td class="price-cell"><?= number_format($p['price'],2) ?> TND</td>
                    <td><?= date('d M Y', strtotime($p['created_at'])) ?></td>
                    <td>
                        <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn-sm btn-edit">✏️ Edit</a>
                        <a href="product_delete.php?id=<?= $p['id'] ?>" class="btn-sm btn-del"
                           onclick="return confirm('Delete this product?')">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
