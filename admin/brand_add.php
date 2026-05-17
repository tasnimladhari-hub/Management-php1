<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']        ?? '');
    $category = trim($_POST['category']    ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $image    = '';

    if (!$name || !$category) {
        $error = 'Name and category are required.';
    } else {

        if (!empty($_FILES['image']['name'])) {
            $ext   = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allow)) {
                $error = 'Invalid image type. Allowed: jpg, jpeg, png, webp, gif.';
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $error = 'Image too large (max 2MB).';
            } else {
                $fname = 'brands/' . uniqid('brand_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fname);
                $image = 'uploads/' . $fname;
            }
        }

        if (!$error) {
            $pdo  = getPDO();
            $stmt = $pdo->prepare('INSERT INTO brands (name,category,description,image) VALUES (?,?,?,?)');
            $stmt->execute([$name, $category, $desc, $image]);
            flashSet('success', "Brand \"$name\" added successfully!");
            header('Location: brands.php');
            exit;
        }
    }
}

define('PAGE_TITLE', 'Add Brand');
$cssRoot = '../';
include '../includes/header.php';
?>

<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo"><img src="../images/logo-removebg-preview.png" alt="HERFA"><span>HERFA Admin</span></div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="slink">📊 Dashboard</a>
            <a href="brands.php" class="slink">🏷️ Brands</a>
            <a href="brand_add.php" class="slink active">➕ Add Brand</a>
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
            <h2>➕ Add New Brand</h2>
            <a href="brands.php" class="btn-ghost">← Back</a>
        </div>

        <?php if ($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="form-card">
            <form method="POST" action="brand_add.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Brand Name *</label>
                    <input type="text" name="name" placeholder="e.g. Dar El Fen"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Category *</label>
                    <input type="text" name="category" placeholder="e.g. Pottery, Textiles, Food..."
                           value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Tell us about this brand..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Brand Image</label>
                    <input type="file" name="image" accept="image/*" class="file-input">
                </div>
                <button type="submit" class="btn-primary">💾 Save Brand</button>
            </form>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
