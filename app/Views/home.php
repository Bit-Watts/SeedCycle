<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<nav class="sc-nav">
  <a href="landing.php" class="sc-logo">Seed<span>Cycle</span></a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="sc-nav-user">
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?> 👋</span>
      <a href="cart.php" class="sc-nav-icon" title="Cart">🛒</a>
      <a href="profile.php" class="sc-nav-icon" title="Profile">👤</a>
      <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
    </div>
  <?php else: ?>
    <div class="sc-nav-user">
      <a href="login.php"><button class="sc-btn-nav" style="background:transparent; color:#fff; border:2px solid rgba(255,255,255,0.5);">Login</button></a>
      <a href="signup.php"><button class="sc-btn-nav">Sign Up</button></a>
    </div>
  <?php endif; ?>
</nav>

  <section class="sc-hero">
    <div class="sc-hero-text">
      <h1>Grow More,<br>Waste <span>Less.</span></h1>
      <p>Buy and sell seeds, and get notified exactly when it's time to plant. SeedCycle makes growing easier for everyone.</p>
      <a href="marketplace.php"><button class="sc-btn-primary">Browse Seeds</button></a>
      <a href="planting-guide.php"><button class="sc-btn-secondary">Start Planting</button></a>
    </div>
    <div class="sc-hero-visual">
      <p class="sc-visual-title">Available Seeds</p>
      <div class="sc-seed-card">
        <div class="sc-seed-icon">🍅</div>
        <div class="sc-seed-info"><p>Tomato Seeds <span class="sc-badge">In Season</span></p><span>Vegetable</span></div>
        <span class="sc-seed-price">₱45</span>
      </div>
      <div class="sc-seed-card">
        <div class="sc-seed-icon">🌿</div>
        <div class="sc-seed-info"><p>Basil Seeds</p><span>Herb</span></div>
        <span class="sc-seed-price">₱30</span>
      </div>
      <div class="sc-seed-card">
        <div class="sc-seed-icon">🌶️</div>
        <div class="sc-seed-info"><p>Chili Seeds</p><span>Vegetable</span></div>
        <span class="sc-seed-price">₱55</span>
      </div>
    </div>
  </section>

  <section class="sc-features">
    <h2>Everything You Need to Grow!</h2>
    <div class="sc-feat-grid">
      <div class="sc-feat-card">
        <div class="sc-feat-icon">🛒</div>
        <h3>Buy & Sell Seeds</h3>
        <p>Browse hundreds of seed varieties or list your own. Simple, fast, and reliable.</p>
      </div>
      <div class="sc-feat-card">
        <div class="sc-feat-icon">🔔</div>
        <h3>Know When to Plant</h3>
        <p>Get notified at the perfect time to plant your seeds based on the season.</p>
      </div>
      <div class="sc-feat-card">
        <div class="sc-feat-icon">🚚</div>
        <h3>Fast Delivery</h3>
        <p>Seeds delivered straight to your door. Fresh stocks, every order.</p>
      </div>
    </div>
  </section>

  <section class="sc-cta">
    <h2>Ready to Start Growing?</h2>
    <p>Join SeedCycle today and plant smarter, not harder.</p>
    <a href="signup.php"><button class="sc-btn-cta">Sign Up for Free</button></a>
  </section>

  <footer class="sc-footer">
    <p>© 2026 SeedCycle. All rights reserved.</p>
  </footer>

<!-- LOGOUT CONFIRMATION MODAL -->
<div class="sc-logout-overlay" id="logoutOverlay">
  <div class="sc-logout-modal">
    <div class="sc-logout-icon">👋</div>
    <h3>Leaving so soon?</h3>
    <p>Are you sure you want to logout?</p>
    <div class="sc-logout-actions">
      <button class="sc-logout-confirm" onclick="window.location.href='logout.php'">Yes, Logout</button>
      <button class="sc-logout-cancel" onclick="document.getElementById('logoutOverlay').classList.remove('active')">Cancel</button>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href="logout.php"]').forEach(function(el) {
      el.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutOverlay').classList.add('active');
      });
    });
  });
</script>

</body>
</html>
