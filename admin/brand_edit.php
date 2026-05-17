<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo = getPDO();
$id  = (int)($_GET['id'] ?? 0);
$brand = $pdo->prepare('SELECT * FROM brands WHERE id=?');
$brand->execute([$id]);
$brand = $brand->fetch();
if (!$brand) { header('Location: brands.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']        ?? '');
    $category = trim($_POST['category']    ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $image    = $brand['image'];

    if (!$name || !$category) {
        $error = 'Name and category are required.';
    } else {
        if (!empty($_FILES['image']['name'])) {
            $ext   = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allow = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allow)) {
                $error = 'Invalid image type.';
            } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                $error = 'Image too large (max 2MB).';
            } else {

                if ($image && str_starts_with($image, 'uploads/') && file_exists('../' . $image)) {
                    unlink('../' . $image);
                }
                $fname = 'brands/' . uniqid('brand_') . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fname);
                $image = 'uploads/' . $fname;
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare('UPDATE brands SET name=?,category=?,description=?,image=? WHERE id=?');
            $stmt->execute([$name, $category, $desc, $image, $id]);
            flashSet('success', "Brand \"$name\" updated.");
            header('Location: brands.php');
            exit;
        }
    }
}

define('PAGE_TITLE', 'Edit Brand');
$cssRoot = '../';
include '../includes/header.php';
?>
<div class="admin-layout">
    <aside class="sidebar">
        <div class="sidebar-logo"><img src="../images/logo-removebg-preview.png" alt="HERFA"><span>HERFA Admin</span></div>
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
            <h2>✏️ Edit Brand</h2>
            <a href="brands.php" class="btn-ghost">← Back</a>
        </div>

        <?php if ($error): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div><?php endif; ?>

        <div class="form-card">
            <?php if ($brand['image']): ?>
            <div class="current-img">
                <img src="../<?= htmlspecialchars($brand['image']) ?>" alt="Current image">
                <p>Current image</p>
            </div>
            <?php endif; ?>
            <form method="POST" action="brand_edit.php?id=<?= $id ?>" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Brand Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($brand['name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Category *</label>
                    <input type="text" name="category" value="<?= htmlspecialchars($brand['category']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($brand['description'] ?? '') ?></textarea>
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
