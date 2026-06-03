<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Orders</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/base.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<nav class="sc-nav">
  <a href="dashboard.php" class="sc-logo"><img src="../assets/images/SeedCycleLogo.png" alt="SeedCycle" class="sc-logo-img"> Seed<span>Cycle</span> <span style="font-size:12px; background:#FFC107; color:#333; padding:2px 8px; border-radius:4px; margin-left:8px; font-family:'Roboto',sans-serif; font-weight:600;">ADMIN</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Admin 👋</span>
    <a href="../logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-dashboard">
  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar">🛡️</div>
      <p class="sc-sidebar-name">Admin Panel</p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="dashboard.php" class="sc-sidebar-link">📊 Dashboard</a>
      <a href="users.php" class="sc-sidebar-link">👥 Users</a>
      <a href="seeds.php" class="sc-sidebar-link">🌱 Seeds</a>
      <a href="listings.php" class="sc-sidebar-link">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link active">🛍️ User Orders</a>
      <a href="shipments.php" class="sc-sidebar-link">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Orders</h1>
      <p>Manage all customer orders.</p>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>All Orders</h2>
        <span style="font-size:12px; color:#888;"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($orders)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No orders yet.</p>
      <?php else: ?>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">#</th>
              <th style="padding:10px 12px; color:#2E7D32;">Customer</th>
              <th style="padding:10px 12px; color:#2E7D32;">Seeds</th>
              <th style="padding:10px 12px; color:#2E7D32;">Total</th>
              <th style="padding:10px 12px; color:#2E7D32;">Delivery</th>
              <th style="padding:10px 12px; color:#2E7D32;">Date</th>
              <th style="padding:10px 12px; color:#2E7D32;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px; font-weight:600; color:#2E7D32;">#<?= (int)$o['id'] ?></td>
              <td style="padding:10px 12px;">
                <?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?><br>
                <span style="font-size:11px; color:#888;"><?= htmlspecialchars($o['email']) ?></span>
              </td>
              <td style="padding:10px 12px; color:#555; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                <?= htmlspecialchars($o['seed_names']) ?>
              </td>
              <td style="padding:10px 12px; font-weight:600; color:#2E7D32;">₱<?= number_format($o['total_amount'], 2) ?></td>
              <td style="padding:10px 12px; color:#555;"><?= htmlspecialchars(ucfirst($o['delivery_method'] ?? '—')) ?></td>
              <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
              <td style="padding:10px 12px;">
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
                  background:#e8f5e9; color:#2E7D32;">
                  <?= htmlspecialchars(ucwords(str_replace('_', ' ', $o['status'] ?? 'pending'))) ?>
                </span>
              </td>
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
  <p>© 2026 SeedCycle Admin.</p>
</footer>

</body>
</html>
