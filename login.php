<?php

require_once 'includes/config.php';

require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');

    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {

        $pdo = getPDO();

        $stmt = $pdo->prepare('SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1');

        $stmt->execute([$username]);

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            flashSet('success', 'Welcome back, ' . $user['username'] . '! ✨');

            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php'));
            exit;
        } else {
            $error = 'Invalid username or password.';
    }
    }
}

define('PAGE_TITLE', 'Login');

$cssRoot = '';

include 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="images/logo-removebg-preview.png" alt="HERFA">
        </div>
        <h1 class="auth-title">Welcome Back 👋</h1>
        <p class="auth-sub">Sign in to your HERFA account</p>

        <?php

        if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <span>👤</span>
                    <input type="text" id="username" name="username"
                           placeholder="Your username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <span>🔒</span>
                    <input type="password" id="password" name="password"
                           placeholder="Your password" required>
                </div>
            </div>
            <button type="submit" class="btn-primary full-width">Sign In ✨</button>
        </form>

        <p class="auth-hint">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<?php

include 'includes/footer.php';
?>
