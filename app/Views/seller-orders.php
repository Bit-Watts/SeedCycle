<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - To Ship</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/seller-orders.css">
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
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link active">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>📦 To Ship</h1>
      <p>Manage orders and shipments for your seeds.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:16px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php
    // Summary stats
    $total     = count($orders);
    $pending   = count(array_filter($orders, fn($o) => $o['shipping_status'] === 'pending'));
    $shipped   = count(array_filter($orders, fn($o) => in_array($o['shipping_status'], ['shipped','in_transit','out_for_delivery'])));
    $delivered = count(array_filter($orders, fn($o) => $o['shipping_status'] === 'delivered'));
    ?>

    <div class="sc-seller-stats">
      <div class="sc-stat-card">
        <div class="sc-stat-icon">📬</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $total ?></span>
          <span class="sc-stat-label">Total Orders</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">⏳</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $pending ?></span>
          <span class="sc-stat-label">To Ship</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🚚</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $shipped ?></span>
          <span class="sc-stat-label">In Transit</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">✅</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $delivered ?></span>
          <span class="sc-stat-label">Delivered</span>
        </div>
      </div>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Orders to Ship</h2>
        <span style="font-size:12px; color:#888;"><?= $total ?> order<?= $total !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($orders)): ?>
        <div class="sc-seller-empty">
          <div class="sc-empty-icon">📭</div>
          <p>No orders for your seeds yet.</p>
          <a href="my-seeds.php" style="color:#2E7D32; font-weight:500;">View your seeds →</a>
        </div>
      <?php else: ?>
        <div class="sc-orders-list">
          <?php 
          // Group orders by order_id to avoid duplicates
          $groupedOrders = [];
          foreach ($orders as $o) {
            $oid = $o['order_id'];
            if (!isset($groupedOrders[$oid])) {
              $groupedOrders[$oid] = $o;
              $groupedOrders[$oid]['seeds'] = [];
            }
            $groupedOrders[$oid]['seeds'][] = [
              'name' => $o['seed_name'],
              'quantity' => $o['quantity'],
              'price' => $o['price']
            ];
          }
          ?>
          
          <?php foreach ($groupedOrders as $o): ?>
          <div class="sc-order-card">
            <div class="sc-order-header">
              <div>
                <span class="sc-order-id">Order #<?= (int)$o['order_id'] ?></span>
                <span class="sc-order-date"><?= date('M j, Y', strtotime($o['created_at'])) ?></span>
              </div>
              <span class="sc-ship-badge sc-ship-<?= htmlspecialchars($o['shipping_status']) ?>">
                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $o['shipping_status']))) ?>
              </span>
            </div>

            <div class="sc-order-body">
              <div class="sc-order-section">
                <h4>🌱 Seeds</h4>
                <?php foreach ($o['seeds'] as $seed): ?>
                  <div class="sc-seed-item">
                    <span><?= htmlspecialchars($seed['name']) ?></span>
                    <span class="sc-seed-qty"><?= (int)$seed['quantity'] ?> pack<?= $seed['quantity'] > 1 ? 's' : '' ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="sc-order-section">
                <h4>👤 Buyer</h4>
                <p><?= htmlspecialchars($o['buyer_first_name'] . ' ' . $o['buyer_last_name']) ?></p>
              </div>

              <div class="sc-order-section">
                <h4>📍 Delivery Address</h4>
                <p>
                  <?php
                  $parts = array_filter([
                    $o['street_address'] ?? '',
                    $o['barangay']       ?? '',
                    $o['city']           ?? '',
                    $o['municipality']   ?? '',
                    $o['province']       ?? '',
                    $o['zip_code']       ?? '',
                  ]);
                  echo htmlspecialchars(implode(', ', $parts));
                  ?>
                </p>
                <p style="margin-top:4px; color:#666; font-size:12px;">
                  <strong>Method:</strong> <?= htmlspecialchars(ucfirst($o['delivery_method'] ?? 'Standard')) ?>
                </p>
              </div>

              <?php if (!empty($o['shipment'])): ?>
              <div class="sc-order-section sc-shipment-info">
                <h4>🚚 Shipment Details</h4>
                <p><strong>Courier:</strong> <?= htmlspecialchars($o['shipment']['courier']) ?></p>
                <p><strong>Tracking #:</strong> <?= htmlspecialchars($o['shipment']['tracking_number']) ?></p>
                <?php if (!empty($o['shipment']['estimated_delivery'])): ?>
                  <p><strong>Est. Delivery:</strong> <?= date('M j, Y', strtotime($o['shipment']['estimated_delivery'])) ?></p>
                <?php endif; ?>
                <button class="sc-btn-update-shipment" onclick="openUpdateShipment(<?= (int)$o['shipment']['id'] ?>, <?= (int)$o['order_id'] ?>, '<?= htmlspecialchars($o['shipment']['courier'], ENT_QUOTES) ?>', '<?= htmlspecialchars($o['shipment']['tracking_number'], ENT_QUOTES) ?>', '<?= htmlspecialchars($o['shipment']['estimated_delivery'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($o['shipment']['status'], ENT_QUOTES) ?>')">
                  Update Shipment
                </button>
              </div>
              <?php else: ?>
              <div class="sc-order-actions">
                <button class="sc-btn-ship" onclick="openCreateShipment(<?= (int)$o['order_id'] ?>)">
                  🚚 Create Shipment
                </button>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<!-- CREATE SHIPMENT MODAL -->
<div class="sc-logout-overlay" id="createShipmentOverlay" onclick="if(event.target===this) closeCreateShipment()">
  <div class="sc-logout-modal" style="max-width:480px;">
    <div class="sc-logout-icon">🚚</div>
    <h3>Create Shipment</h3>
    <form method="POST" action="seller-orders.php">
      <input type="hidden" name="action" value="create_shipment">
      <input type="hidden" name="order_id" id="createOrderId">
      
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Courier</label>
        <select name="courier" required style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
          <option value="">Select courier</option>
          <option value="LBC">LBC</option>
          <option value="J&T Express">J&T Express</option>
          <option value="Ninja Van">Ninja Van</option>
          <option value="JRS Express">JRS Express</option>
          <option value="Flash Express">Flash Express</option>
          <option value="Lalamove">Lalamove</option>
          <option value="Grab Express">Grab Express</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Tracking Number</label>
        <input type="text" name="tracking_number" placeholder="e.g. LBC123456789" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Estimated Delivery (Optional)</label>
        <input type="date" name="estimated_delivery"
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
      </div>

      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Create Shipment</button>
        <button type="button" class="sc-logout-cancel" onclick="closeCreateShipment()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- UPDATE SHIPMENT MODAL -->
<div class="sc-logout-overlay" id="updateShipmentOverlay" onclick="if(event.target===this) closeUpdateShipment()">
  <div class="sc-logout-modal" style="max-width:480px;">
    <div class="sc-logout-icon">📦</div>
    <h3>Update Shipment</h3>
    <form method="POST" action="seller-orders.php">
      <input type="hidden" name="action" value="update_shipment">
      <input type="hidden" name="shipment_id" id="updateShipmentId">
      
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Courier</label>
        <select name="courier" id="updateCourier" required style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
          <option value="">Select courier</option>
          <option value="LBC">LBC</option>
          <option value="J&T Express">J&T Express</option>
          <option value="Ninja Van">Ninja Van</option>
          <option value="JRS Express">JRS Express</option>
          <option value="Flash Express">Flash Express</option>
          <option value="Lalamove">Lalamove</option>
          <option value="Grab Express">Grab Express</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Tracking Number</label>
        <input type="text" name="tracking_number" id="updateTrackingNumber" placeholder="e.g. LBC123456789" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Estimated Delivery</label>
        <input type="date" name="estimated_delivery" id="updateEstimatedDelivery"
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
      </div>

      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Status</label>
        <select name="status" id="updateStatus" required style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none;">
          <option value="shipped">Shipped</option>
          <option value="in_transit">In Transit</option>
          <option value="out_for_delivery">Out for Delivery</option>
          <option value="delivered">Delivered</option>
        </select>
      </div>

      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Update Shipment</button>
        <button type="button" class="sc-logout-cancel" onclick="closeUpdateShipment()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openCreateShipment(orderId) {
    document.getElementById('createOrderId').value = orderId;
    document.getElementById('createShipmentOverlay').classList.add('active');
  }
  
  function closeCreateShipment() {
    document.getElementById('createShipmentOverlay').classList.remove('active');
  }

  function openUpdateShipment(shipmentId, orderId, courier, trackingNumber, estimatedDelivery, status) {
    document.getElementById('updateShipmentId').value = shipmentId;
    document.getElementById('updateCourier').value = courier;
    document.getElementById('updateTrackingNumber').value = trackingNumber;
    document.getElementById('updateEstimatedDelivery').value = estimatedDelivery;
    document.getElementById('updateStatus').value = status;
    document.getElementById('updateShipmentOverlay').classList.add('active');
  }
  
  function closeUpdateShipment() {
    document.getElementById('updateShipmentOverlay').classList.remove('active');
  }

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
