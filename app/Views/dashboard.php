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
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?> 👋</span>
    <a href="cart.php" class="sc-nav-icon" title="Cart">🛒</a>
    <a href="profile.php" class="sc-nav-icon" title="Profile">👤</a>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-dashboard">

  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar" style="overflow:hidden;">
        <?php $pi = $user['profile_image'] ?? $_SESSION['profile_image'] ?? ''; ?>
        <?php if (!empty($pi)): ?>
          <img src="<?= htmlspecialchars($pi) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link active">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?>!</h1>
      <p>Here's what's growing with your account today.</p>
    </div>

    <div class="sc-stats-grid">
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🌾</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($listingsCount ?? 0) ?></span>
          <span class="sc-stat-label">Seeds Listed</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🛒</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($ordersCount ?? 0) ?></span>
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

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Recommended Seeds</h2>
        <a href="marketplace.php" class="sc-section-link">View all →</a>
      </div>
      <div class="sc-seed-grid">
        <?php if (!empty($recommendedSeeds)): ?>
          <?php foreach ($recommendedSeeds as $rs): ?>
          <div class="sc-seed-card">
            <div class="sc-seed-emoji">
              <?php if (!empty($rs['image_url'])): ?>
                <img src="<?= htmlspecialchars($rs['image_url']) ?>" alt="<?= htmlspecialchars($rs['name']) ?>"
                     style="width:44px; height:44px; object-fit:cover; border-radius:10px;">
              <?php else: ?>
                🌱
              <?php endif; ?>
            </div>
            <div class="sc-seed-details">
              <p class="sc-seed-name"><?= htmlspecialchars($rs['name']) ?></p>
              <p class="sc-seed-type"><?= htmlspecialchars($rs['category'] ?? 'Seed') ?></p>
              <p class="sc-seed-tip">📅 <?= htmlspecialchars($rs['month_range']) ?></p>
            </div>
            <span class="sc-seed-price">₱<?= number_format($rs['price'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="color:#888; font-size:13px;">No seeds available right now. <a href="marketplace.php" style="color:#4CAF50;">Browse marketplace →</a></p>
        <?php endif; ?>
      </div>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Planting Schedule Preview</h2>
        <a href="planting-guide.php" class="sc-section-link">Full guide →</a>
      </div>
      <div class="sc-schedule-list">
        <?php
        $currentMonth = (int)date('n');
        if (!empty($scheduleSeeds)):
          $monthsShown = 0;
          foreach ($scheduleSeeds as $m => $seeds):
            if ($monthsShown >= 3) break;
            $monthsShown++;
            $isNow     = $seeds[0]['is_now'];
            $monthAbbr = $seeds[0]['month_abbr'];
            $cropNames = implode(', ', array_map(fn($s) => '🌱 ' . htmlspecialchars($s['name']), $seeds));
        ?>
        <div class="sc-schedule-item">
          <div class="sc-schedule-month"><?= $monthAbbr ?></div>
          <div class="sc-schedule-info">
            <p class="sc-schedule-crop"><?= $cropNames ?></p>
            <p class="sc-schedule-tip">Best planting time for these seeds.</p>
          </div>
          <span class="sc-schedule-badge <?= $isNow ? 'active' : 'upcoming' ?>"><?= $isNow ? 'Now' : 'Upcoming' ?></span>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
          <p style="color:#888; font-size:13px;">No planting schedule data available. <a href="planting-guide.php" style="color:#4CAF50;">View full guide →</a></p>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>

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
