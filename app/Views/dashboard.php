<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'dashboard'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Welcome back, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?>!</h1>
      <p>Here's what's growing with your account today.</p>
    </div>

    <div class="sc-stats-grid">
      <div class="sc-stat-card">
        <div class="sc-stat-icon"><i class="fa-solid fa-wheat-awn"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($listingsCount ?? 0) ?></span>
          <span class="sc-stat-label">Seeds Listed</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon"><i class="fa-solid fa-cart-shopping"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($ordersCount ?? 0) ?></span>
          <span class="sc-stat-label">Orders Placed</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon"><i class="fa-solid fa-box"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($ordersReceivedCount ?? 0) ?></span>
          <span class="sc-stat-label">Orders Received</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon"><i class="fa-solid fa-bell"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($notificationsCount ?? 0) ?></span>
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
          <a href="seed-details.php?id=<?= (int)$rs['id'] ?>" class="sc-seed-card-link">
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
              <p class="sc-seed-tip"><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars($rs['month_range']) ?></p>
            </div>
            <span class="sc-seed-price">₱<?= number_format($rs['price'], 2) ?></span>
          </div>
          </a>
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

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>
</body>
</html>
