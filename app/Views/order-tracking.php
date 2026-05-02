<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Order Tracking</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/order-tracking.css">
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

<?php
// Define timeline steps
$steps = [
    'pending'          => ['label' => 'Order Placed',    'icon' => '📋'],
    'processing'       => ['label' => 'Processing',      'icon' => '⚙️'],
    'shipped'          => ['label' => 'Shipped',          'icon' => '📦'],
    'in_transit'       => ['label' => 'In Transit',       'icon' => '🚛'],
    'out_for_delivery' => ['label' => 'Out for Delivery', 'icon' => '🚚'],
    'delivered'        => ['label' => 'Delivered',        'icon' => '🏠'],
];

$statusOrder   = array_keys($steps);
$currentStatus = $order['shipping_status'] ?? $order['status'] ?? 'pending';
// Normalize: if order status is processing but no shipping status yet
if ($currentStatus === 'pending' && ($order['status'] ?? '') === 'processing') {
    $currentStatus = 'processing';
}
$currentIndex = array_search($currentStatus, $statusOrder);
if ($currentIndex === false) $currentIndex = 0;
?>

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

<div class="sc-tracking-page">

  <div class="sc-tracking-header">
    <h1>Order #<?= (int)$order['id'] ?> Tracking</h1>
    <a href="orders.php" class="sc-back-link">← Back to Orders</a>
  </div>

  <!-- TIMELINE -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>Shipment Status</h2>
      <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
      </span>
    </div>

    <div class="sc-timeline">
      <?php foreach ($steps as $key => $step):
        $idx = array_search($key, $statusOrder);
        $isDone    = $idx < $currentIndex;
        $isCurrent = $idx === $currentIndex;
        $cls = $isDone ? 'done' : ($isCurrent ? 'current' : '');
      ?>
      <div class="sc-timeline-step <?= $cls ?>">
        <div class="sc-timeline-dot"><?= $step['icon'] ?></div>
        <div class="sc-timeline-label"><?= $step['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- SHIPMENT INFO -->
  <?php if ($shipment): ?>
  <div class="sc-tracking-grid">
    <div class="sc-info-card">
      <h3>📦 Shipment Details</h3>
      <div class="sc-info-row">
        <span class="sc-info-label">Courier</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Tracking Number</span>
        <span class="sc-info-value"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Est. Delivery</span>
        <span class="sc-info-value">
          <?= $shipment['estimated_delivery'] ? date('M j, Y', strtotime($shipment['estimated_delivery'])) : '—' ?>
        </span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Shipment Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
          </span>
        </span>
      </div>
    </div>

    <div class="sc-info-card">
      <h3>📍 Delivery Address</h3>
      <?php if (!empty($order['street_address'])): ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Street</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['street_address']) ?></span>
      </div>
      <?php endif; ?>
      <div class="sc-info-row">
        <span class="sc-info-label">Barangay</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['barangay'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">City / Municipality</span>
        <span class="sc-info-value"><?= htmlspecialchars(($order['city'] ?? '') . ', ' . ($order['municipality'] ?? '')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Province</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">ZIP Code</span>
        <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
      </div>
    </div>
  </div>
  <?php else: ?>
  <div class="sc-no-shipment">
    <span style="font-size:22px;">📭</span>
    <div>
      <strong>Shipment details not yet available.</strong><br>
      <span>Your order is being processed. Tracking information will appear here once your order is shipped.</span>
    </div>
  </div>
  <?php endif; ?>

  <!-- ORDER ITEMS -->
  <div class="sc-section">
    <div class="sc-section-header">
      <h2>🌱 Items Ordered</h2>
      <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
    </div>
    <table style="width:100%; border-collapse:collapse; font-size:13px;">
      <thead>
        <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
          <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
          <th style="padding:10px 12px; color:#2E7D32;">Qty</th>
          <th style="padding:10px 12px; color:#2E7D32;">Unit Price</th>
          <th style="padding:10px 12px; color:#2E7D32;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orderItems as $item): ?>
        <tr style="border-bottom:1px solid #f0f0f0;">
          <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($item['name']) ?></td>
          <td style="padding:10px 12px; color:#555;"><?= (int)$item['quantity'] ?></td>
          <td style="padding:10px 12px; color:#555;">₱<?= number_format($item['price'], 2) ?></td>
          <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" style="padding:12px 12px; text-align:right; font-weight:600; color:#333;">Total:</td>
          <td style="padding:12px 12px; font-family:'Poppins',sans-serif; font-size:16px; font-weight:700; color:#2E7D32;">
            ₱<?= number_format($order['total_amount'], 2) ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </div>

  <!-- ORDER META -->
  <div class="sc-section">
    <div class="sc-section-header"><h2>📋 Order Info</h2></div>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
      <div class="sc-info-row">
        <span class="sc-info-label">Order ID</span>
        <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Date</span>
        <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Delivery Method</span>
        <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
      </div>
      <div class="sc-info-row">
        <span class="sc-info-label">Order Status</span>
        <span class="sc-info-value">
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
            <?= htmlspecialchars(ucfirst($order['status'])) ?>
          </span>
        </span>
      </div>
    </div>
  </div>

</div><!-- end sc-tracking-page -->

  </main>
</div><!-- end sc-dashboard -->

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
