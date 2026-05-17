<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) { header('Location: login.php'); exit; }

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // In a real project you'd send an email here (mail() or PHPMailer)
        $success = "Thank you, $name! Your message has been sent. We'll get back to you soon. ✨";
    }
}

define('PAGE_TITLE', 'Contact Us');
$cssRoot = '';
include 'includes/header.php';
?>

<header class="hero">
    <img src="images/logo-removebg-preview.png" alt="HERFA Logo" class="logo">
    <h1 class="hero-title">Contact Us</h1>
    <p class="hero-sub">We'd love to hear from you</p>
    <nav class="hero-nav">
        <a href="index.php"    class="nav-link">🏠 Home</a>
        <a href="products.php" class="nav-link">🛍️ Products</a>
        <a href="about.php"    class="nav-link">🌿 About</a>
        <a href="contact.php"  class="nav-link active">📬 Contact</a>
        <?php if (isAdmin()): ?>
            <a href="admin/dashboard.php" class="nav-link nav-admin">⚡ Admin</a>
        <?php endif; ?>
        <?php if (isLoggedIn()): ?>
            <?php if (!isAdmin()): ?>
                <a href="cart.php" class="nav-link nav-cart">🛒 Cart</a>
            <?php endif; ?>
            <a href="logout.php" class="nav-link nav-logout">Sign Out</a>
        <?php endif; ?>
    </nav>
</header>

<section class="container">

    <div class="section-header">
        <h2 class="section-title">📬 Get In Touch</h2>
        <p class="section-sub">Have a question? We're here to help</p>
    </div>

    <div class="contact-layout">

        <!-- Contact Form -->
        <div class="contact-form-wrap">

            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" id="contactForm" novalidate>
                <div class="form-group">
                    <label for="name">👤 Full Name</label>
                    <input type="text" id="name" name="name"
                           placeholder="Your name"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    <span class="field-error" id="nameErr"></span>
                </div>

                <div class="form-group">
                    <label for="email">📧 Email Address</label>
                    <input type="email" id="email" name="email"
                           placeholder="your@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <span class="field-error" id="emailErr"></span>
                </div>

                <div class="form-group">
                    <label for="subject">📝 Subject</label>
                    <input type="text" id="subject" name="subject"
                           placeholder="What's this about?"
                           value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                    <span class="field-error" id="subjectErr"></span>
                </div>

                <div class="form-group">
                    <label for="message">💬 Message</label>
                    <textarea id="message" name="message"
                              placeholder="Write your message here..."
                              rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                    <span class="field-error" id="messageErr"></span>
                </div>

                <button type="submit" class="btn-primary full-width">Send Message 🚀</button>
            </form>
        </div>

        <!-- Contact Info + Map -->
        <div class="contact-info-wrap">

            <div class="contact-info-card">
                <h3>📍 Our Location</h3>
                <p>123 Rue de la Médina<br>Msaken 4070 Sousse,Tunisia</p>
            </div>

            <div class="contact-info-card">
                <h3>📞 Phone</h3>
                <p>+216 71 000 000</p>
            </div>

            <div class="contact-info-card">
                <h3>📧 Email</h3>
                <p>contact@herfa.tn</p>
            </div>

            <div class="contact-info-card">
                <h3>🕐 Working Hours</h3>
                <p>Monday – Friday: 9:00 AM – 5:00 PM<br>Saturday: 10:00 AM – 3:00 PM</p>
            </div>

            <!-- Google Maps embed — Tunis city center -->
            <div class="map-wrap">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3193.1!2d10.1815!3d36.8190!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x12fd337f5e7ef543%3A0xd671924e714a0275!2sTunis%2C%20Tunisia!5e0!3m2!1sen!2stn!4v1"
                    width="100%" height="220"
                    style="border:0; border-radius:12px;"
                    allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

        </div>
    </div>
</section>

<style>
/* Contact layout */
.contact-layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 36px;
    align-items: start;
    margin-top: 8px;
}

.contact-form-wrap {
    background: white;
    border-radius: var(--radius);
    padding: 30px 28px;
    box-shadow: var(--shadow-sm);
}

/* Info cards */
.contact-info-wrap { display: flex; flex-direction: column; gap: 14px; }
.contact-info-card {
    background: white;
    border-radius: var(--radius-sm);
    padding: 16px 20px;
    box-shadow: var(--shadow-sm);
    border-left: 4px solid var(--purple);
}
.contact-info-card h3 { font-size: .95rem; color: var(--purple); margin-bottom: 4px; }
.contact-info-card p  { font-size: 13.5px; color: #555; line-height: 1.6; }

/* Map */
.map-wrap { border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); }

/* Field error messages */
.field-error { display: block; color: #c62828; font-size: 12px; margin-top: 4px; min-height: 16px; }

@media (max-width: 768px) {
    .contact-layout { grid-template-columns: 1fr; }
}
</style>

<script>
// JS form validation (client-side)
document.getElementById('contactForm').addEventListener('submit', function(e) {
    let valid = true;

    const fields = [
        { id: 'name',    errId: 'nameErr',    msg: 'Name is required.' },
        { id: 'subject', errId: 'subjectErr', msg: 'Subject is required.' },
        { id: 'message', errId: 'messageErr', msg: 'Message is required.' },
    ];

    // Clear all errors first
    document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

    fields.forEach(f => {
        const val = document.getElementById(f.id).value.trim();
        if (!val) {
            document.getElementById(f.errId).textContent = f.msg;
            valid = false;
        }
    });

    // Email validation
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!email) {
        document.getElementById('emailErr').textContent = 'Email is required.';
        valid = false;
    } else if (!emailRegex.test(email)) {
        document.getElementById('emailErr').textContent = 'Please enter a valid email.';
        valid = false;
    }

    if (!valid) e.preventDefault(); // Stop submission if invalid
});
</script>

<script src="script.js"></script>
<?php include 'includes/footer.php'; ?>