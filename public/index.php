<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

  <!-- Navbar -->
  <nav class="sc-nav">
    <div class="sc-logo">Seed<span>Cycle</span></div>
    <ul class="sc-navlinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="marketplace.php">Marketplace</a></li>
      <li><a href="planting-guide.php">Planting Guide</a></li>
    </ul>
    <a href="login.php"><button class="sc-btn-nav">Login</button></a>
  </nav>

  <!-- Hero -->
  <section class="sc-hero">
    <div class="sc-hero-text">
      <h1>Grow More,<br>Waste <span>Less.</span></h1>
      <p>Buy and sell seeds, and get notified exactly when it's time to plant. SeedCycle makes growing easier for everyone.</p>
      <a href="marketplace.php"><button class="sc-btn-primary">Browse Seeds</button></a>
      <a href="planting-guide.php"><button class="sc-btn-secondary">Start Planting</button></a>
    </div>

    <div class="sc-hero-visual">
       later lain na di i-code, nga halin gd sa inventory ang ma dispaly. atm naka hardcode pa lang
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

  <!-- Features -->
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

  <!-- CTA -->
  <section class="sc-cta">
    <h2>Ready to Start Growing?</h2>
    <p>Join SeedCycle today and plant smarter, not harder.</p>
    <a href="signup.php"><button class="sc-btn-cta">Sign Up for Free</button></a>
  </section>

  <!-- Footer -->
  <footer class="sc-footer">
    <p>© 2026 SeedCycle. All rights reserved.</p>
    <div class="sc-footer-links">
      <a href="index.php">Home</a>
      <a href="marketplace.php">Marketplace</a>
      <a href="planting-guide.php">Planting Guide</a>
    </div>
  </footer>

</body>
</html>