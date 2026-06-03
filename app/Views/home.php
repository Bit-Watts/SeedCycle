<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<nav class="sc-nav">
  <a href="landing.php" class="sc-logo"><img src="assets/images/SeedCycleLogo.png" alt="SeedCycle" class="sc-logo-img"> Seed<span>Cycle</span></a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="sc-nav-user">
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?></span>
      <a href="cart.php" class="sc-nav-icon" title="Cart"><i class="fa-solid fa-cart-shopping"></i></a>
      <a href="profile.php" class="sc-nav-icon" title="Profile"><i class="fa-solid fa-user"></i></a>
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
      <div class="sc-hero-btn-group">
        <a href="marketplace.php"><button class="sc-hero-btn sc-hero-btn-outline">Browse Seeds</button></a>
        <a href="planting-guide.php"><button class="sc-hero-btn sc-hero-btn-yellow">Start Planting</button></a>
      </div>
    </div>
    <div class="sc-hero-visual">
      <p class="sc-visual-title">Available Seeds</p>
      <?php if (!empty($landingSeeds)): ?>
        <?php foreach ($landingSeeds as $ls): ?>
        <a href="marketplace.php" style="text-decoration:none; color:inherit;">
          <div class="sc-seed-card">
            <div class="sc-seed-icon">
              <?php if (!empty($ls['image_url'])): ?>
                <img src="<?= htmlspecialchars($ls['image_url']) ?>" alt="<?= htmlspecialchars($ls['name']) ?>"
                     style="width:36px; height:36px; object-fit:cover; border-radius:8px;">
              <?php else: ?>
                <i class="fa-solid fa-seedling"></i>
              <?php endif; ?>
            </div>
            <div class="sc-seed-info">
              <p><?= htmlspecialchars($ls['name']) ?>
                <?php if ($ls['in_season']): ?>
                  <span class="sc-badge">In Season</span>
                <?php endif; ?>
              </p>
              <span><?= htmlspecialchars($ls['category'] ?? 'Seed') ?></span>
            </div>
            <span class="sc-seed-price">₱<?= number_format($ls['price'], 0) ?></span>
          </div>
        </a>
        <?php endforeach; ?>
      <?php else: ?>
        <div style="text-align:center; padding:20px; color:#888; font-size:13px;">
          No seeds available yet. <a href="marketplace.php" style="color:#4CAF50;">Browse marketplace →</a>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <section class="sc-features">
    <h2>Everything You Need to Grow!</h2>
    <div class="sc-feat-grid">
      <div class="sc-feat-card">
        <div class="sc-feat-icon"><i class="fa-solid fa-store"></i></div>
        <h3>Buy & Sell Seeds</h3>
        <p>Browse hundreds of seed varieties or list your own. Simple, fast, and reliable.</p>
      </div>
      <div class="sc-feat-card">
        <div class="sc-feat-icon"><i class="fa-solid fa-bell"></i></div>
        <h3>Know When to Plant</h3>
        <p>Get notified at the perfect time to plant your seeds based on the season.</p>
      </div>
      <div class="sc-feat-card">
        <div class="sc-feat-icon"><i class="fa-solid fa-truck"></i></div>
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
    <div class="sc-logout-icon"><i class="fa-solid fa-right-from-bracket"></i></div>
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
