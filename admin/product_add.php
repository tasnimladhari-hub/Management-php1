<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo    = getPDO();
$brands = $pdo->query('SELECT id, name FROM brands ORDER BY name')->fetchAll();
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']        ?? '');
    $brand_id = (int)($_POST['brand_id']   ?? 0);
    $price    = trim($_POST['price']       ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $image    = '';

    if (!$name || !$brand_id || $price === '') {
        $error = 'Name, brand, and price are required.';
    } elseif (!is_numeric($price) || (float)$price < 0) {
        $error = 'Price must be a valid positive number.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $ext   = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allow)) { $error = 'Invalid image type.'; }
            elseif ($_FILES['image']['size'] > 2*1024*1024) { $error = 'Image too large.'; }
            else {
                $fname = 'products/' . uniqid('prod_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fname);
                $image = 'uploads/' . $fname;
            }
        }
        if (!$error) {
            $stmt = $pdo->prepare('INSERT INTO products (name,brand_id,price,description,image) VALUES (?,?,?,?,?)');
            $stmt->execute([$name, $brand_id, (float)$price, $desc, $image]);
            flashSet('success', "Product \"$name\" added!");
            header('Location: products.php');
            exit;
        }
    }
}

define('PAGE_TITLE', 'Add Product');
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
            <a href="products.php" class="slink">🛍️ Products</a>
            <a href="product_add.php" class="slink active">➕ Add Product</a>
            <a href="users.php" class="slink">👥 Users</a>
            <hr class="sidebar-hr">
            <a href="../index.php" class="slink">🌐 View Site</a>
            <a href="../logout.php" class="slink slink-logout">🚪 Sign Out</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-topbar"><h2>➕ Add Product</h2><a href="products.php" class="btn-ghost">← Back</a></div>
        <?php if ($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="form-card">
            <form method="POST" action="product_add.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" placeholder="e.g. Olive Oil 500ml"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Brand *</label>
                    <select name="brand_id" required>
                        <option value="">Select brand...</option>
                        <?php foreach ($brands as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= (($_POST['brand_id'] ?? '') == $b['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (TND) *</label>
                    <input type="number" name="price" step="0.01" min="0" placeholder="0.00"
                           value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Product details..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Product Image</label>
                    <input type="file" name="image" accept="image/*" class="file-input">
                </div>
                <button type="submit" class="btn-primary">💾 Save Product</button>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
