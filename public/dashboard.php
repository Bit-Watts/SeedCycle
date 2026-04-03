<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php">Marketplace</a></li>
    <li><a href="planting-guide.php">Planting Guide</a></li>
  </ul>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?> 👋</span>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<!-- DASHBOARD LAYOUT -->
<div class="sc-dashboard">

  <!-- SIDEBAR -->
  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar">🌱</div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? 'user@email.com') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="dashboard.php" class="sc-sidebar-link active">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">📦 Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?>!</h1>
      <p>Here's what's growing with your account today.</p>
    </div>

    <!-- QUICK OVERVIEW -->
    <div class="sc-stats-grid">
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🌾</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value">0</span>
          <span class="sc-stat-label">Seeds Listed</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🛒</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value">0</span>
          <span class="sc-stat-label">Orders Placed</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">📦</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value">0</span>
          <span class="sc-stat-label">Orders Received</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🔔</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value">0</span>
          <span class="sc-stat-label">Notifications</span>
        </div>
      </div>
    </div>

    <!-- RECOMMENDED SEEDS (hardcoded for now, replace with inventory query later) -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Recommended Seeds</h2>
        <a href="marketplace.php" class="sc-section-link">View all →</a>
      </div>
      <div class="sc-seed-grid">

        <div class="sc-seed-card">
          <div class="sc-seed-emoji">🍅</div>
          <div class="sc-seed-details">
            <p class="sc-seed-name">Tomato Seeds <span class="sc-badge sc-badge-season">In Season</span></p>
            <p class="sc-seed-type">Vegetable</p>
            <p class="sc-seed-tip">📅 Apr – Jun &nbsp;|&nbsp; ☀️ Dry Season</p>
          </div>
          <span class="sc-seed-price">₱45</span>
        </div>

        <div class="sc-seed-card">
          <div class="sc-seed-emoji">🌿</div>
          <div class="sc-seed-details">
            <p class="sc-seed-name">Basil Seeds</p>
            <p class="sc-seed-type">Herb</p>
            <p class="sc-seed-tip">📅 Mar – May &nbsp;|&nbsp; ☀️ Dry Season</p>
          </div>
          <span class="sc-seed-price">₱30</span>
        </div>

        <div class="sc-seed-card">
          <div class="sc-seed-emoji">🌶️</div>
          <div class="sc-seed-details">
            <p class="sc-seed-name">Chili Seeds</p>
            <p class="sc-seed-type">Vegetable</p>
            <p class="sc-seed-tip">📅 Feb – Apr &nbsp;|&nbsp; ☀️ Dry Season</p>
          </div>
          <span class="sc-seed-price">₱55</span>
        </div>

        <div class="sc-seed-card">
          <div class="sc-seed-emoji">🥬</div>
          <div class="sc-seed-details">
            <p class="sc-seed-name">Pechay Seeds <span class="sc-badge sc-badge-popular">Popular</span></p>
            <p class="sc-seed-type">Vegetable</p>
            <p class="sc-seed-tip">📅 Year-round &nbsp;|&nbsp; 🌧️ All Seasons</p>
          </div>
          <span class="sc-seed-price">₱25</span>
        </div>

      </div>
    </div>

    <!-- PLANTING TIPS / SCHEDULE PREVIEW -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Planting Schedule Preview</h2>
        <a href="planting-guide.php" class="sc-section-link">Full guide →</a>
      </div>
      <div class="sc-schedule-list">

        <div class="sc-schedule-item">
          <div class="sc-schedule-month">APR</div>
          <div class="sc-schedule-info">
            <p class="sc-schedule-crop">🍅 Tomato, 🌶️ Chili, 🧅 Onion</p>
            <p class="sc-schedule-tip">Start seeds indoors 6–8 weeks before last frost. Transplant when soil warms up.</p>
          </div>
          <span class="sc-schedule-badge active">Now</span>
        </div>

        <div class="sc-schedule-item">
          <div class="sc-schedule-month">MAY</div>
          <div class="sc-schedule-info">
            <p class="sc-schedule-crop">🌽 Corn, 🥒 Cucumber, 🎃 Squash</p>
            <p class="sc-schedule-tip">Direct sow after last frost. Ensure full sun and consistent watering.</p>
          </div>
          <span class="sc-schedule-badge upcoming">Upcoming</span>
        </div>

        <div class="sc-schedule-item">
          <div class="sc-schedule-month">JUN</div>
          <div class="sc-schedule-info">
            <p class="sc-schedule-crop">🫘 Beans, 🥬 Pechay, 🌿 Basil</p>
            <p class="sc-schedule-tip">Rainy season planting. Great for leafy greens and legumes.</p>
          </div>
          <span class="sc-schedule-badge upcoming">Upcoming</span>
        </div>

      </div>
    </div>

  </main>
</div>

<!-- FOOTER -->
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
