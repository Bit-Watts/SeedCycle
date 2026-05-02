<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? $user['first_name'] ?? 'Grower') ?> 👋</span>
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
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image']??"";?><?php if(!empty($pi)):?><img src="<?=htmlspecialchars($pi)?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><?php else:?>🌱<?php endif;?></div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image'] ?? ''; ?>
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
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image']??"";?><?php if(!empty($pi)):?><img src="<?=htmlspecialchars($pi)?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><?php else:?>🌱<?php endif;?></div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">🌱</div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td>🌱 <?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td><span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500; background:#e8f5e9; color:#2E7D32;"><?= htmlspecialchars(ucfirst($p['status'])) ?></span></td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
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
