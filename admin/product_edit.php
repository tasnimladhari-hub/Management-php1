<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo = getPDO();
$id  = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) { header('Location: products.php'); exit; }

$brands = $pdo->query('SELECT id, name FROM brands ORDER BY name')->fetchAll();
$error  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']        ?? '');
    $brand_id = (int)($_POST['brand_id']   ?? 0);
    $price    = trim($_POST['price']       ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $image    = $product['image'];

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
                if ($image && str_starts_with($image, 'uploads/') && file_exists('../'.$image)) unlink('../'.$image);
                $fname = 'products/' . uniqid('prod_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fname);
                $image = 'uploads/' . $fname;
            }
        }
        if (!$error) {
            $stmt = $pdo->prepare('UPDATE products SET name=?,brand_id=?,price=?,description=?,image=? WHERE id=?');
            $stmt->execute([$name, $brand_id, (float)$price, $desc, $image, $id]);
            flashSet('success', "Product \"$name\" updated.");
            header('Location: products.php');
            exit;
        }
    }
}

define('PAGE_TITLE', 'Edit Product');
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
        <div class="admin-topbar"><h2>✏️ Edit Product</h2><a href="products.php" class="btn-ghost">← Back</a></div>
        <?php if ($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>
        <div class="form-card">
            <?php if ($product['image']): ?>
            <div class="current-img"><img src="../<?= htmlspecialchars($product['image']) ?>" alt=""><p>Current image</p></div>
            <?php endif; ?>
            <form method="POST" action="product_edit.php?id=<?= $id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Brand *</label>
                    <select name="brand_id" required>
                        <?php foreach ($brands as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= $product['brand_id'] == $b['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (TND) *</label>
                    <input type="number" name="price" step="0.01" min="0" value="<?= $product['price'] ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>New Image (leave blank to keep current)</label>
                    <input type="file" name="image" accept="image/*" class="file-input">
                </div>
                <button type="submit" class="btn-primary">💾 Save Changes</button>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
