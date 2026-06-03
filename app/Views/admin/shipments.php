<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Shipments</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('../assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('../assets/css/admin.css') ?>">
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
      <a href="orders.php" class="sc-sidebar-link">🛍️ User Orders</a>
      <a href="shipments.php" class="sc-sidebar-link active">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <?php /* <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a> */ ?>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Shipments</h1>
      <p>Manage order shipments and tracking.</p>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>All Shipments</h2>
        <span style="font-size:12px; color:#888;"><?= count($shipments) ?> shipment<?= count($shipments) !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($shipments)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No shipments yet.</p>
      <?php else: ?>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">Order #</th>
              <th style="padding:10px 12px; color:#2E7D32;">Customer</th>
              <th style="padding:10px 12px; color:#2E7D32;">Courier</th>
              <th style="padding:10px 12px; color:#2E7D32;">Tracking #</th>
              <th style="padding:10px 12px; color:#2E7D32;">Est. Delivery</th>
              <th style="padding:10px 12px; color:#2E7D32;">Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($shipments as $s): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px; font-weight:600; color:#2E7D32;">#<?= (int)$s['order_id'] ?></td>
              <td style="padding:10px 12px;"><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
              <td style="padding:10px 12px;"><?= htmlspecialchars($s['courier']) ?></td>
              <td style="padding:10px 12px; font-family:monospace; font-size:12px;"><?= htmlspecialchars($s['tracking_number']) ?></td>
              <td style="padding:10px 12px; color:#555;">
                <?= $s['estimated_delivery'] ? date('M j, Y', strtotime($s['estimated_delivery'])) : '—' ?>
              </td>
              <td style="padding:10px 12px;">
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
                  background:#e8f5e9; color:#2E7D32;">
                  <?= htmlspecialchars(ucwords(str_replace('_', ' ', $s['status'] ?? 'pending'))) ?>
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
