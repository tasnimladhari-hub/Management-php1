<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo    = getPDO();
$brands = $pdo->query('SELECT b.*, (SELECT COUNT(*) FROM products WHERE brand_id=b.id) AS product_count FROM brands b ORDER BY b.created_at DESC')->fetchAll();
$flash  = flashGet('success');
$flashE = flashGet('error');

define('PAGE_TITLE', 'Manage Brands');
$cssRoot = '../';
include '../includes/header.php';
?>

<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="../images/logo-removebg-preview.png" alt="HERFA"><span>HERFA Admin</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="slink">📊 Dashboard</a>
            <a href="brands.php" class="slink active">🏷️ Brands</a>
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
            <h2>🏷️ All Brands</h2>
            <a href="brand_add.php" class="btn-primary">➕ Add Brand</a>
        </div>

        <?php if ($flash): ?><div class="alert alert-success">✅ <?= htmlspecialchars($flash) ?></div><?php endif; ?>
        <?php if ($flashE): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($flashE) ?></div><?php endif; ?>

        <div class="admin-panel">
            <table class="admin-table">
                <thead>
                    <tr><th>#</th><th>Image</th><th>Name</th><th>Category</th><th>Products</th><th>Added</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($brands as $b): ?>
                <tr>
                    <td><?= $b['id'] ?></td>
                    <td>
                        <?php $img = $b['image'] ?? ''; ?>
                        <img src="../<?= $img && file_exists('../' . $img) ? htmlspecialchars($img) : 'images/potterie.png' ?>"
                             class="thumb" alt="">
                    </td>
                    <td><strong><?= htmlspecialchars($b['name']) ?></strong></td>
                    <td><span class="chip"><?= htmlspecialchars($b['category']) ?></span></td>
                    <td><?= $b['product_count'] ?></td>
                    <td><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                    <td>
                        <a href="brand_edit.php?id=<?= $b['id'] ?>" class="btn-sm btn-edit">✏️ Edit</a>
                        <a href="brand_delete.php?id=<?= $b['id'] ?>" class="btn-sm btn-del"
                           onclick="return confirm('Delete «<?= addslashes($b['name']) ?>» and all its products?')">🗑️ Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
