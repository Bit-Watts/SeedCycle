<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span> Admin</a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Admin 👋</span>
    <a href="../logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-dashboard">

  <!-- SIDEBAR -->
  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar">🛡️</div>
      <p class="sc-sidebar-name">Admin Panel</p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link active">📊 Dashboard</a>
      <a href="users.php" class="sc-sidebar-link">👥 Users</a>
      <a href="seeds.php" class="sc-sidebar-link">🌱 Seeds</a>
      <a href="listings.php" class="sc-sidebar-link">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="shipments.php" class="sc-sidebar-link">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Admin Dashboard</h1>
      <p>Overview of the SeedCycle platform.</p>
    </div>

    <!-- STATS -->
    <div class="sc-stats-grid">
      <div class="sc-stat-card">
        <div class="sc-stat-icon">👥</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)$totalUsers ?></span>
          <span class="sc-stat-label">Total Users</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">📦</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)$totalOrders ?></span>
          <span class="sc-stat-label">Total Orders</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🌱</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)$totalSeeds ?></span>
          <span class="sc-stat-label">Active Seeds</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">⏳</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)$pendingListings ?></span>
          <span class="sc-stat-label">Pending Listings</span>
        </div>
      </div>
    </div>

    <!-- RECENT ORDERS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Recent Orders</h2>
        <a href="orders.php" class="sc-section-link">View all →</a>
      </div>

      <?php if (empty($recentOrders)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No orders yet.</p>
      <?php else: ?>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">Order #</th>
              <th style="padding:10px 12px; color:#2E7D32;">Customer</th>
              <th style="padding:10px 12px; color:#2E7D32;">Total</th>
              <th style="padding:10px 12px; color:#2E7D32;">Status</th>
              <th style="padding:10px 12px; color:#2E7D32;">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentOrders as $o): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px; font-weight:600; color:#2E7D32;">#<?= (int)$o['id'] ?></td>
              <td style="padding:10px 12px;"><?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?></td>
              <td style="padding:10px 12px; font-weight:600;">₱<?= number_format($o['total_amount'], 2) ?></td>
              <td style="padding:10px 12px;">
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
                  background:#e8f5e9; color:#2E7D32;">
                  <?= htmlspecialchars(ucfirst($o['status'])) ?>
                </span>
              </td>
              <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
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
  <p>© 2026 SeedCycle Admin. All rights reserved.</p>
</footer>

</body>
</html>
