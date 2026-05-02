<?php
$currentMonth = (int) date('n');

// Build month-indexed structure from DB seeds (month names already pre-formatted by controller)
$monthData = [];
for ($m = 1; $m <= 12; $m++) {
    $monthData[$m] = [];
}

if (!empty($plantingSeeds)) {
    foreach ($plantingSeeds as $seed) {
        $start = (int)($seed['planting_start_month'] ?? 0);
        $end   = (int)($seed['planting_end_month']   ?? $start);
        if ($start < 1) continue;
        for ($m = $start; $m <= min($end, 12); $m++) {
            $monthData[$m][] = $seed;
        }
    }
}

$monthNames = ['','January','February','March','April','May','June',
               'July','August','September','October','November','December'];
$monthNums  = ['','01','02','03','04','05','06','07','08','09','10','11','12'];
$seasons    = [1=>'Dry',2=>'Dry',3=>'Dry',4=>'Dry',5=>'Wet',6=>'Wet',
               7=>'Wet',8=>'Wet',9=>'Wet',10=>'Dry',11=>'Dry',12=>'Dry'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Planting Guide</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/planting-guide.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?> 👋</span>
    <a href="cart.php" class="sc-nav-icon" title="Cart">🛒</a>
    <a href="profile.php" class="sc-nav-icon" title="Profile">👤</a>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-dashboard">

  <?php if (isset($_SESSION['user_id'])): ?>
  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar" style="overflow:hidden;">
        <?php $pi = $_SESSION['profile_image'] ?? ''; ?>
        <?php if (!empty($pi)): ?>
          <img src="<?= htmlspecialchars($pi) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link active">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>
  <?php endif; ?>

  <main class="sc-main">

    <!-- HEADER + CONTROLS -->
    <div class="sc-guide-header" style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px;">
      <div>
        <h1>Planting Guide</h1>
        <p>Best planting times for every seed, every season.</p>
      </div>
      <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
        <select id="month-filter" onchange="filterByMonth()"
          style="padding:8px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:'Roboto',sans-serif; color:#333; outline:none; background:#fff;">
          <option value="">All Months</option>
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>"><?= $monthNames[$m] ?></option>
          <?php endfor; ?>
        </select>
        <div style="display:flex; gap:6px;">
          <button class="sc-toggle-btn active" id="btn-calendar" onclick="switchView('calendar')">📅 Calendar</button>
          <button class="sc-toggle-btn" id="btn-list" onclick="switchView('list')">📋 List</button>
        </div>
      </div>
    </div>

    <!-- CALENDAR VIEW -->
    <div id="view-calendar">
      <div class="sc-calendar-grid">
        <?php for ($m = 1; $m <= 12; $m++):
          $season = $seasons[$m];
          $crops  = $monthData[$m];
        ?>
        <div class="sc-cal-card <?= $m === $currentMonth ? 'current' : '' ?>" data-month="<?= $m ?>">
          <div class="sc-cal-header">
            <span class="sc-cal-month"><?= $monthNames[$m] ?></span>
            <span class="sc-cal-season <?= strtolower($season) ?>"><?= $season === 'Dry' ? '☀️ Dry' : '🌧️ Wet' ?></span>
          </div>
          <ul class="sc-cal-crops">
            <?php if (!empty($crops)): ?>
              <?php foreach ($crops as $crop): ?>
                <li>🌱 <?= htmlspecialchars($crop['name']) ?></li>
              <?php endforeach; ?>
            <?php else: ?>
              <li style="color:#bbb; font-style:italic;">No seeds this month</li>
            <?php endif; ?>
          </ul>
        </div>
        <?php endfor; ?>
      </div>
    </div>

    <!-- LIST VIEW -->
    <div id="view-list" style="display:none;">
      <div class="sc-list-wrap">
        <?php for ($m = 1; $m <= 12; $m++):
          $season = $seasons[$m];
          $crops  = $monthData[$m];
        ?>
        <div class="sc-list-row <?= $m === $currentMonth ? 'current' : '' ?>" data-month="<?= $m ?>">
          <div class="sc-list-month">
            <span class="sc-list-num"><?= $monthNums[$m] ?></span>
            <span class="sc-list-name"><?= $monthNames[$m] ?></span>
            <span class="sc-list-season <?= strtolower($season) ?>"><?= $season === 'Dry' ? '☀️ Dry' : '🌧️ Wet' ?></span>
          </div>
          <div class="sc-list-crops">
            <?php if (!empty($crops)): ?>
              <?php foreach ($crops as $crop): ?>
                <span class="sc-crop-tag">🌱 <?= htmlspecialchars($crop['name']) ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              <span style="font-size:12px; color:#bbb; font-style:italic;">No seeds this month</span>
            <?php endif; ?>
          </div>
          <?php if ($m === $currentMonth): ?>
            <span class="sc-now-badge">Now</span>
          <?php endif; ?>
        </div>
        <?php endfor; ?>
      </div>
    </div>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  function switchView(view) {
    document.getElementById('view-calendar').style.display = view === 'calendar' ? 'block' : 'none';
    document.getElementById('view-list').style.display     = view === 'list'     ? 'block' : 'none';
    document.getElementById('btn-calendar').classList.toggle('active', view === 'calendar');
    document.getElementById('btn-list').classList.toggle('active', view === 'list');
  }

  function filterByMonth() {
    const val      = document.getElementById('month-filter').value;
    const calCards = document.querySelectorAll('.sc-cal-card');
    const listRows = document.querySelectorAll('.sc-list-row');

    calCards.forEach(card => {
      card.style.display = (!val || card.dataset.month === val) ? '' : 'none';
    });
    listRows.forEach(row => {
      row.style.display = (!val || row.dataset.month === val) ? '' : 'none';
    });
  }
</script>


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