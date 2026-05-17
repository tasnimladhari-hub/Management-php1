<?php

require_once 'includes/config.php';

require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');

    $email    = trim($_POST['email']    ?? '');

    $password = $_POST['password']      ?? '';

    $confirm  = $_POST['confirm']       ?? '';

    if (!$username || !$email || !$password || !$confirm) {

        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {

        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {

        $error = 'Passwords do not match.';
    } else {

        $pdo = getPDO();

        $check = $pdo->prepare('SELECT id FROM users WHERE username=? OR email=?');
        $check->execute([$username, $email]);

        if ($check->fetch()) {

            $error = 'Username or email already taken.';
        } else {

            $hash = password_hash($password, PASSWORD_BCRYPT);

            $ins = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
            $ins->execute([$username, $email, $hash, 'user']);

            $success = 'Account created! You can now <a href="login.php">sign in</a>.';
        }
    }
}

define('PAGE_TITLE', 'Register');

$cssRoot = '';

include 'includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <img src="images/logo-removebg-preview.png" alt="HERFA">
        </div>
        <h1 class="auth-title">Join HERFA 🌿</h1>
        <p class="auth-sub">Create your artisan account</p>

        <?php

        if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            
            <div class="alert alert-success">✅ <?= $success ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Username</label>
                <div class="input-icon"><span>👤</span>
                    <input type="text" name="username" placeholder="Choose a username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <div class="input-icon"><span>📧</span>
                    <input type="email" name="email" placeholder="your@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="input-icon"><span>🔒</span>
                    <input type="password" name="password" placeholder="Min 6 characters" required>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div class="input-icon"><span>🔑</span>
                    <input type="password" name="confirm" placeholder="Repeat password" required>
                </div>
            </div>
            <button type="submit" class="btn-primary full-width">Create Account 🎉</button>
        </form>

        <p class="auth-hint">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
</div>

<?php

include 'includes/footer.php';
?>
