<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Shipments</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span> <span style="font-size:12px; background:#FFC107; color:#333; padding:2px 8px; border-radius:4px; margin-left:8px; font-family:'Roboto',sans-serif; font-weight:600;">ADMIN</span></div>
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
      <a href="index.php" class="sc-sidebar-link">📊 Dashboard</a>
      <a href="users.php" class="sc-sidebar-link">👥 Users</a>
      <a href="seeds.php" class="sc-sidebar-link">🌱 Seeds</a>
      <a href="listings.php" class="sc-sidebar-link">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="shipments.php" class="sc-sidebar-link active">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Shipments</h1>
      <p>Manage order shipments and tracking.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- ADD SHIPMENT FORM -->
    <?php if (!empty($unshippedOrders)): ?>
    <div class="sc-admin-form">
      <h3>➕ Add Shipment</h3>
      <form method="POST" action="shipments.php">
        <input type="hidden" name="action" value="add">
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Order *</label>
            <select name="order_id" required>
              <option value="">Select order</option>
              <?php foreach ($unshippedOrders as $uo): ?>
                <option value="<?= (int)$uo['id'] ?>">#<?= (int)$uo['id'] ?> — <?= htmlspecialchars($uo['first_name'] . ' ' . $uo['last_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="sc-form-group">
            <label>Courier *</label>
            <input type="text" name="courier" required placeholder="e.g. LBC, J&T, Ninja Van">
          </div>
        </div>
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Tracking Number *</label>
            <input type="text" name="tracking_number" required placeholder="e.g. LBC123456789">
          </div>
          <div class="sc-form-group">
            <label>Estimated Delivery</label>
            <input type="date" name="estimated_delivery">
          </div>
        </div>
        <div class="sc-form-group" style="max-width:200px;">
          <label>Status</label>
          <select name="status">
            <option value="pending">Pending</option>
            <option value="shipped">Shipped</option>
            <option value="in_transit">In Transit</option>
            <option value="out_for_delivery">Out for Delivery</option>
            <option value="delivered">Delivered</option>
          </select>
        </div>
        <button type="submit" class="sc-btn-submit">Add Shipment</button>
      </form>
    </div>
    <?php endif; ?>

    <!-- SHIPMENTS TABLE -->
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
              <th style="padding:10px 12px; color:#2E7D32;">Update</th>
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
              <td style="padding:10px 12px;">
                <form method="POST" action="shipments.php" style="display:flex; gap:6px; align-items:center;">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="shipment_id" value="<?= (int)$s['id'] ?>">
                  <input type="hidden" name="courier" value="<?= htmlspecialchars($s['courier']) ?>">
                  <input type="hidden" name="tracking_number" value="<?= htmlspecialchars($s['tracking_number']) ?>">
                  <input type="hidden" name="estimated_delivery" value="<?= htmlspecialchars($s['estimated_delivery'] ?? '') ?>">
                  <select name="status" style="padding:5px 8px; border:1.5px solid #c8e6c9; border-radius:6px; font-size:12px; font-family:'Roboto',sans-serif; color:#333; outline:none; background:#fff;">
                    <?php foreach (['pending','shipped','in_transit','out_for_delivery','delivered'] as $st): ?>
                      <option value="<?= $st ?>" <?= ($s['status'] ?? '') === $st ? 'selected' : '' ?>>
                        <?= ucwords(str_replace('_', ' ', $st)) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" style="background:#4CAF50; color:#fff; border:none; padding:5px 10px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif;">Save</button>
                </form>
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
