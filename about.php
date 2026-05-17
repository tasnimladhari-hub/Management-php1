<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) { header('Location: login.php'); exit; }

define('PAGE_TITLE', 'About Us');
$cssRoot = '';
include 'includes/header.php';
?>

<header class="hero">
    <img src="images/logo-removebg-preview.png" alt="HERFA Logo" class="logo">
    <h1 class="hero-title">About HERFA</h1>
    <p class="hero-sub">Our Story, Our Mission, Our Team</p>
    <nav class="hero-nav">
        <a href="index.php"   class="nav-link">🏠 Home</a>
        <a href="products.php" class="nav-link">🛍️ Products</a>
        <a href="about.php"   class="nav-link active">🌿 About</a>
        <a href="contact.php" class="nav-link">📬 Contact</a>
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

    <!-- Our Story -->
    <div class="section-header">
        <h2 class="section-title">🌿 Our Story</h2>
        <p class="section-sub">How HERFA was born from a passion for tradition</p>
    </div>

    <div class="about-story">
        <p>
            HERFA was founded in 2026 with a simple but powerful mission: to bring the rich heritage of Tunisian
            artisanship to the modern world. The word <strong>"Herfa"</strong> (حرفة) means <em>craft</em> in Arabic —
            and that is exactly what we celebrate every day.
        </p>
        <p>
            From hand-woven carpets in Kairouan to painted pottery in Nabeul, Tunisia has always been home to
            extraordinary artisans whose skills have been passed down for generations. HERFA exists to give these
            talented makers a platform, a voice, and a market.
        </p>
    </div>

    <!-- Values -->
    <div class="section-header" style="margin-top:56px;">
        <h2 class="section-title">💎 Our Values</h2>
        <p class="section-sub">What we stand for</p>
    </div>

    <div class="values-grid">
        <div class="value-card">
            <span class="value-icon">🤝</span>
            <h3>Authenticity</h3>
            <p>Every brand on HERFA is hand-vetted. We only feature genuine artisans who create by hand.</p>
        </div>
        <div class="value-card">
            <span class="value-icon">🌍</span>
            <h3>Heritage</h3>
            <p>We preserve and celebrate Tunisia's centuries-old craft traditions for future generations.</p>
        </div>
        <div class="value-card">
            <span class="value-icon">⭐</span>
            <h3>Quality</h3>
            <p>Handmade means each piece is unique. We prioritize quality and craftsmanship above all.</p>
        </div>
        <div class="value-card">
            <span class="value-icon">💬</span>
            <h3>Community</h3>
            <p>We build bridges between artisans and customers, creating a thriving creative community.</p>
        </div>
    </div>

    <!-- Team -->
    <div class="section-header" style="margin-top:56px;">
        <h2 class="section-title">👥 Meet the Team</h2>
        <p class="section-sub">The people behind HERFA</p>
    </div>

    <div class="team-grid">
        <div class="team-card">
            <div class="team-avatar">👩‍💼</div>
            <h3>Tasnim Ladhari</h3>
            <span class="team-role">Founder & CEO</span>
            <p>Passionate about Tunisian culture, Tasnim started HERFA to support local artisans and share their work with the world.</p>
        </div>
        <div class="team-card">
            <div class="team-avatar">👩‍🎨</div>
            <h3>Rim Zarrouk</h3>
            <span class="team-role">Lead Developer</span>
            <p>Rim built the HERFA platform from the ground up, ensuring a smooth and beautiful experience for all users.</p>
        </div>
       
    </div>

</section>

<style>
/* About page specific styles */
.about-story {
    max-width: 720px;
    margin: 0 auto;
    font-size: 15.5px;
    line-height: 1.85;
    color: #444;
    text-align: center;
}
.about-story p { margin-bottom: 16px; }

/* Values */
.values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 22px;
    margin-top: 12px;
}
.value-card {
    background: white;
    border-radius: var(--radius);
    padding: 28px 22px;
    text-align: center;
    box-shadow: var(--shadow-sm);
    border-top: 4px solid var(--pink);
    transition: transform .3s, box-shadow .3s;
}
.value-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); }
.value-icon { font-size: 2.4rem; display: block; margin-bottom: 12px; }
.value-card h3 { color: var(--purple); font-size: 1.05rem; margin-bottom: 8px; }
.value-card p  { font-size: 13.5px; color: #666; line-height: 1.6; }

/* Team */
.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 24px;
    margin-top: 12px;
}
.team-card {
    background: white;
    border-radius: var(--radius);
    padding: 32px 24px;
    text-align: center;
    box-shadow: var(--shadow-sm);
    border-bottom: 4px solid var(--purple);
    transition: transform .3s, box-shadow .3s;
}
.team-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); }
.team-avatar {
    font-size: 3.5rem;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f0eaff, #ffe6f4);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 16px;
    border: 3px solid var(--border);
    transition: transform .3s;
}
.team-card:hover .team-avatar { transform: scale(1.1); }
.team-card h3 { color: var(--purple); font-size: 1.05rem; margin-bottom: 4px; }
.team-role {
    display: inline-block;
    background: linear-gradient(135deg, var(--purple), var(--pink));
    color: white;
    font-size: 11px; font-weight: 700;
    padding: 3px 12px; border-radius: 20px;
    margin-bottom: 12px; letter-spacing: .5px;
}
.team-card p { font-size: 13px; color: #666; line-height: 1.6; margin-top: 8px; }
</style>

<script src="script.js"></script>
<?php include 'includes/footer.php'; ?>