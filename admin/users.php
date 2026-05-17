<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$pdo   = getPDO();
$flash = flashGet('success');
$flashE= flashGet('error');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_role'])) {
    $uid = (int)$_POST['user_id'];
    if ($uid !== (int)currentUser()['id']) {
        $u = $pdo->prepare('SELECT role FROM users WHERE id=?'); $u->execute([$uid]);
        $currentRole = $u->fetchColumn();
        $newRole = $currentRole === 'admin' ? 'user' : 'admin';
        $pdo->prepare('UPDATE users SET role=? WHERE id=?')->execute([$newRole, $uid]);
        flashSet('success', 'User role updated.');
    }
    header('Location: users.php'); exit;
}

if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid !== (int)currentUser()['id']) {
        $pdo->prepare('DELETE FROM users WHERE id=?')->execute([$uid]);
        flashSet('success', 'User deleted.');
    } else {
        flashSet('error', "You can't delete your own account.");
    }
    header('Location: users.php'); exit;
}

$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();

define('PAGE_TITLE', 'Manage Users');
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
            <a href="product_add.php" class="slink">➕ Add Product</a>
            <a href="users.php" class="slink active">👥 Users</a>
            <hr class="sidebar-hr">
            <a href="../index.php" class="slink">🌐 View Site</a>
            <a href="../logout.php" class="slink slink-logout">🚪 Sign Out</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-topbar"><h2>👥 User Management</h2></div>
        <?php if ($flash): ?><div class="alert alert-success">✅ <?= htmlspecialchars($flash) ?></div><?php endif; ?>
        <?php if ($flashE): ?><div class="alert alert-error">⚠️ <?= htmlspecialchars($flashE) ?></div><?php endif; ?>

        <div class="admin-panel">
            <table class="admin-table">
                <thead><tr><th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Joined</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): $isMe = $u['id'] == currentUser()['id']; ?>
                <tr <?= $isMe ? 'class="row-me"' : '' ?>>
                    <td><?= $u['id'] ?></td>
                    <td><strong><?= htmlspecialchars($u['username']) ?></strong> <?= $isMe ? '<span class="chip chip-you">you</span>' : '' ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <span class="chip <?= $u['role'] === 'admin' ? 'chip-admin' : '' ?>">
                            <?= $u['role'] ?>
                        </span>
                    </td>
                    <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if (!$isMe): ?>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="toggle_role" value="1">
                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                            <button type="submit" class="btn-sm btn-edit">
                                <?= $u['role'] === 'admin' ? '⬇️ Make User' : '⬆️ Make Admin' ?>
                            </button>
                        </form>
                        <a href="users.php?delete=<?= $u['id'] ?>" class="btn-sm btn-del"
                           onclick="return confirm('Delete this user?')">🗑️ Delete</a>
                        <?php else: ?>
                        <span class="muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php include '../includes/footer.php'; ?>
