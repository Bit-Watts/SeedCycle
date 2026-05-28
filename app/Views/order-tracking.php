<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Track Order #<?= (int)$order['id'] ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/order-tracking.css">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<?php
// ── TIMELINE STEPS ──
$steps = [
    'pending'          => ['label' => 'Order Confirmed',   'icon' => 'fa-clipboard-check',   'desc' => 'Your order has been placed'],
    'packed'           => ['label' => 'Packed',            'icon' => 'fa-box',                'desc' => 'Seeds are being packed'],
    'shipped'          => ['label' => 'Shipped',           'icon' => 'fa-paper-plane',        'desc' => 'Package handed to courier'],
    'in_transit'       => ['label' => 'In Transit',        'icon' => 'fa-truck',              'desc' => 'On the way to you'],
    'out_for_delivery' => ['label' => 'Out for Delivery',  'icon' => 'fa-truck-fast',         'desc' => 'Arriving today'],
    'delivered'        => ['label' => 'Delivered',         'icon' => 'fa-house-circle-check', 'desc' => 'Package delivered'],
];

$statusOrder   = array_keys($steps);
$currentStatus = $shipment['status'] ?? $order['shipping_status'] ?? 'pending';
if (!in_array($currentStatus, $statusOrder)) $currentStatus = 'pending';
$currentIndex  = array_search($currentStatus, $statusOrder);
?>

<div class="sc-dashboard">

  <?php $activePage = 'orders'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">
    <div class="sc-tracking-page">

      <!-- HEADER -->
      <div class="sc-tracking-header">
        <div>
          <h1><i class="fa-solid fa-location-crosshairs"></i> Track Order #<?= (int)$order['id'] ?></h1>
          <p class="sc-tracking-subtitle">
            Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
          </p>
        </div>
        <a href="orders.php" class="sc-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Orders</a>
      </div>

      <!-- TIMELINE -->
      <div class="sc-section">
        <div class="sc-section-header">
          <h2>Shipment Progress</h2>
          <span class="sc-status-badge sc-status-<?= htmlspecialchars($currentStatus) ?>">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $currentStatus))) ?>
          </span>
        </div>

        <div class="sc-timeline">
          <?php foreach ($steps as $key => $step):
            $idx       = array_search($key, $statusOrder);
            $isDone    = $idx < $currentIndex;
            $isCurrent = $idx === $currentIndex;
            $cls       = $isDone ? 'done' : ($isCurrent ? 'current' : '');
          ?>
          <div class="sc-timeline-step <?= $cls ?>">
            <?php if ($idx > 0): ?>
              <div class="sc-timeline-connector <?= $isDone || $isCurrent ? 'filled' : '' ?>"></div>
            <?php endif; ?>
            <div class="sc-timeline-dot">
              <?php if ($isDone): ?>
                <i class="fa-solid fa-check"></i>
              <?php else: ?>
                <i class="fa-solid <?= $step['icon'] ?>"></i>
              <?php endif; ?>
            </div>
            <div class="sc-timeline-label"><?= $step['label'] ?></div>
            <div class="sc-timeline-desc"><?= $step['desc'] ?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <?php if ($currentStatus === 'delivered'): ?>
          <div class="sc-delivered-banner">
            <i class="fa-solid fa-circle-check"></i>
            <div>
              <strong>Package Delivered!</strong>
              <span>Your seeds have arrived. Happy planting!</span>
            </div>
          </div>
        <?php elseif ($currentStatus === 'out_for_delivery'): ?>
          <div class="sc-ofd-banner">
            <i class="fa-solid fa-truck-fast"></i>
            <div>
              <strong>Out for Delivery Today!</strong>
              <span>Your package is on its way to you right now.</span>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- SHIPMENT DETAILS + ADDRESS -->
      <?php if ($shipment): ?>
      <div class="sc-tracking-grid">
        <div class="sc-info-card">
          <h3><i class="fa-solid fa-truck"></i> Shipment Details</h3>
          <div class="sc-info-row">
            <span class="sc-info-label">Courier</span>
            <span class="sc-info-value sc-courier-badge"><?= htmlspecialchars($shipment['courier'] ?? '—') ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Tracking Number</span>
            <span class="sc-info-value">
              <span class="sc-tracking-num"><?= htmlspecialchars($shipment['tracking_number'] ?? '—') ?></span>
              <button class="sc-copy-btn" onclick="copyTracking('<?= htmlspecialchars($shipment['tracking_number'] ?? '', ENT_QUOTES) ?>')" title="Copy">
                <i class="fa-regular fa-copy"></i>
              </button>
            </span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Est. Delivery</span>
            <span class="sc-info-value sc-est-delivery">
              <?php if (!empty($shipment['estimated_delivery'])): ?>
                <i class="fa-regular fa-calendar"></i>
                <?= date('F j, Y', strtotime($shipment['estimated_delivery'])) ?>
                <?php
                $today = new DateTime();
                $est   = new DateTime($shipment['estimated_delivery']);
                $diff  = (int)$today->diff($est)->days;
                $sign  = $today->diff($est)->invert;
                if ($currentStatus !== 'delivered'):
                  if ($sign === 0 && $diff === 0): ?>
                    <span class="sc-eta-chip sc-eta-today">Today</span>
                  <?php elseif ($sign === 0 && $diff === 1): ?>
                    <span class="sc-eta-chip sc-eta-soon">Tomorrow</span>
                  <?php elseif ($sign === 0 && $diff <= 3): ?>
                    <span class="sc-eta-chip sc-eta-soon"><?= $diff ?> days</span>
                  <?php elseif ($sign === 1): ?>
                    <span class="sc-eta-chip sc-eta-late">Overdue</span>
                  <?php endif; ?>
                <?php endif; ?>
              <?php else: ?>
                <span style="color:#aaa;">Not set</span>
              <?php endif; ?>
            </span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Status</span>
            <span class="sc-info-value">
              <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipment['status'] ?? 'pending') ?>">
                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipment['status'] ?? 'pending'))) ?>
              </span>
            </span>
          </div>
          <?php if (!empty($shipment['notes'])): ?>
          <div class="sc-info-row">
            <span class="sc-info-label">Notes</span>
            <span class="sc-info-value" style="font-style:italic; color:#666;"><?= htmlspecialchars($shipment['notes']) ?></span>
          </div>
          <?php endif; ?>
        </div>

        <div class="sc-info-card">
          <h3><i class="fa-solid fa-location-dot"></i> Delivery Address</h3>
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
            <span class="sc-info-label">City</span>
            <span class="sc-info-value"><?= htmlspecialchars($order['city'] ?? '—') ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Municipality</span>
            <span class="sc-info-value"><?= htmlspecialchars($order['municipality'] ?? '—') ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Province</span>
            <span class="sc-info-value"><?= htmlspecialchars($order['province'] ?? '—') ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">ZIP Code</span>
            <span class="sc-info-value"><?= htmlspecialchars($order['zip_code'] ?? '—') ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Method</span>
            <span class="sc-info-value">
              <i class="fa-solid fa-<?= ($order['delivery_method'] ?? '') === 'pickup' ? 'person-walking' : 'truck' ?>"></i>
              <?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?>
            </span>
          </div>
        </div>
      </div>

      <!-- SHIPMENT HISTORY LOG -->
      <?php if (!empty($shipmentLogs)): ?>
      <div class="sc-section">
        <div class="sc-section-header">
          <h2><i class="fa-solid fa-clock-rotate-left"></i> Shipment History</h2>
        </div>
        <div class="sc-history-list">
          <?php foreach (array_reverse($shipmentLogs) as $log): ?>
          <div class="sc-history-item">
            <div class="sc-history-dot sc-history-dot--<?= htmlspecialchars($log['status']) ?>"></div>
            <div class="sc-history-content">
              <span class="sc-history-status">
                <span class="sc-status-badge sc-status-<?= htmlspecialchars($log['status']) ?>">
                  <?= htmlspecialchars(ucwords(str_replace('_', ' ', $log['status']))) ?>
                </span>
              </span>
              <?php if (!empty($log['note'])): ?>
                <span class="sc-history-note"><?= htmlspecialchars($log['note']) ?></span>
              <?php endif; ?>
              <span class="sc-history-time">
                <i class="fa-regular fa-clock"></i>
                <?= date('M j, Y g:i A', strtotime($log['created_at'])) ?>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php else: ?>
      <div class="sc-no-shipment">
        <i class="fa-solid fa-box-open"></i>
        <div>
          <strong>Shipment details not yet available.</strong>
          <span>Your order is being prepared. Tracking info will appear here once the seller ships your order.</span>
        </div>
      </div>
      <?php endif; ?>

      <!-- ORDER ITEMS -->
      <div class="sc-section">
        <div class="sc-section-header">
          <h2><i class="fa-solid fa-seedling"></i> Items Ordered</h2>
          <span style="font-size:12px; color:#888;"><?= count($orderItems) ?> item<?= count($orderItems) !== 1 ? 's' : '' ?></span>
        </div>
        <div class="sc-items-table-wrap">
          <table class="sc-items-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orderItems as $item): ?>
              <tr>
                <td><i class="fa-solid fa-seedling" style="color:#4CAF50;"></i> <?= htmlspecialchars($item['name']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td>&#8369;<?= number_format($item['price'], 2) ?></td>
                <td class="sc-subtotal">&#8369;<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" class="sc-total-label">Total</td>
                <td class="sc-total-value">&#8369;<?= number_format($order['total_amount'], 2) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- ORDER INFO -->
      <div class="sc-section">
        <div class="sc-section-header"><h2><i class="fa-solid fa-clipboard-list"></i> Order Info</h2></div>
        <div class="sc-tracking-grid">
          <div class="sc-info-row">
            <span class="sc-info-label">Order ID</span>
            <span class="sc-info-value">#<?= (int)$order['id'] ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Order Date</span>
            <span class="sc-info-value"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Order Status</span>
            <span class="sc-info-value">
              <span class="sc-status-badge sc-status-<?= htmlspecialchars($order['status']) ?>">
                <?= htmlspecialchars(ucfirst($order['status'])) ?>
              </span>
            </span>
          </div>
          <div class="sc-info-row">
            <span class="sc-info-label">Delivery Method</span>
            <span class="sc-info-value"><?= htmlspecialchars(ucfirst($order['delivery_method'] ?? '—')) ?></span>
          </div>
        </div>
      </div>

    </div><!-- end sc-tracking-page -->
  </main>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

<script>
function copyTracking(text) {
  if (!text) return;
  navigator.clipboard.writeText(text).then(function() {
    const btn = document.querySelector('.sc-copy-btn');
    btn.innerHTML = '<i class="fa-solid fa-check"></i>';
    setTimeout(() => btn.innerHTML = '<i class="fa-regular fa-copy"></i>', 2000);
  });
}
</script>
</body>
</html>
