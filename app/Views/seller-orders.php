<?php
// Ensure Shipment class is available for static method calls
if (!class_exists('Shipment')) {
    require_once __DIR__ . '/../Models/Shipment.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Seller Orders</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/seller-orders.css">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'seller-orders'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">

    <div class="sc-main-header">
      <div>
        <h1><i class="fa-solid fa-envelope-open-text"></i> Seller Orders</h1>
        <p>View and manage orders for your seeds.</p>
      </div>

    </div>

    <?php if (!empty($message)): ?>
      <div class="sc-alert sc-alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="sc-alert sc-alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php
    $total     = count($orders);
    $pending   = count(array_filter($orders, fn($o) => in_array($o['shipping_status'], ['pending', 'packed'])));
    $shipped   = count(array_filter($orders, fn($o) => in_array($o['shipping_status'], ['shipped','in_transit','out_for_delivery'])));
    $delivered = count(array_filter($orders, fn($o) => $o['shipping_status'] === 'delivered'));
    ?>

    <!-- STATS -->
    <div class="sc-seller-stats">
      <div class="sc-stat-card">
        <div class="sc-stat-icon"><i class="fa-solid fa-box-open"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $total ?></span>
          <span class="sc-stat-label">Total Orders</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon sc-stat-icon--warn"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $pending ?></span>
          <span class="sc-stat-label">To Ship</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon sc-stat-icon--blue"><i class="fa-solid fa-truck"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $shipped ?></span>
          <span class="sc-stat-label">In Transit</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon sc-stat-icon--green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $delivered ?></span>
          <span class="sc-stat-label">Delivered</span>
        </div>
      </div>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>All Orders</h2>
        <span style="font-size:12px; color:#888;"><?= $total ?> order<?= $total !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($orders)): ?>
        <div class="sc-seller-empty">
          <i class="fa-solid fa-box-open" style="font-size:48px; color:#c8e6c9; margin-bottom:16px;"></i>
          <p>No orders for your seeds yet.</p>
          <a href="my-seeds.php" style="color:#2E7D32; font-weight:500;">View your seeds &rarr;</a>
        </div>
      <?php else: ?>
        <div class="sc-orders-list">
          <?php
          // Group orders by order_id
          $groupedOrders = [];
          foreach ($orders as $o) {
            $oid = $o['order_id'];
            if (!isset($groupedOrders[$oid])) {
              $groupedOrders[$oid] = $o;
              $groupedOrders[$oid]['seeds'] = [];
            }
            $groupedOrders[$oid]['seeds'][] = [
              'name'     => $o['seed_name'],
              'quantity' => $o['quantity'],
              'price'    => $o['price'],
            ];
          }
          ?>

          <?php foreach ($groupedOrders as $o): ?>
          <div class="sc-order-card">
            <div class="sc-order-header">
              <div class="sc-order-header-left">
                <span class="sc-order-id">Order #<?= (int)$o['order_id'] ?></span>
                <span class="sc-order-date"><i class="fa-regular fa-calendar"></i> <?= date('M j, Y', strtotime($o['created_at'])) ?></span>
              </div>
              <span class="sc-ship-badge sc-ship-<?= htmlspecialchars($o['shipping_status']) ?>">
                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $o['shipping_status']))) ?>
              </span>
            </div>

            <div class="sc-order-body">
              <div class="sc-order-section">
                <h4><i class="fa-solid fa-seedling"></i> Seeds Ordered</h4>
                <?php foreach ($o['seeds'] as $seed): ?>
                  <div class="sc-seed-item">
                    <span><?= htmlspecialchars($seed['name']) ?></span>
                    <span class="sc-seed-qty"><?= (int)$seed['quantity'] ?> pack<?= $seed['quantity'] > 1 ? 's' : '' ?></span>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="sc-order-section">
                <h4><i class="fa-solid fa-user"></i> Buyer</h4>
                <p><?= htmlspecialchars($o['buyer_first_name'] . ' ' . $o['buyer_last_name']) ?></p>
              </div>

              <div class="sc-order-section">
                <h4><i class="fa-solid fa-location-dot"></i> Delivery Address</h4>
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
                <p class="sc-delivery-method">
                  <i class="fa-solid fa-<?= ($o['delivery_method'] ?? '') === 'pickup' ? 'person-walking' : 'truck' ?>"></i>
                  <?= htmlspecialchars(ucfirst($o['delivery_method'] ?? 'Standard')) ?>
                </p>
              </div>

              <?php if (!empty($o['shipment'])): ?>
              <div class="sc-order-section sc-shipment-info">
                <h4><i class="fa-solid fa-truck"></i> Shipment Details</h4>
                <p><strong>Courier:</strong> <?= htmlspecialchars($o['shipment']['courier']) ?></p>
                <p><strong>Tracking #:</strong> <span class="sc-tracking-num"><?= htmlspecialchars($o['shipment']['tracking_number']) ?></span></p>
                <?php if (!empty($o['shipment']['estimated_delivery'])): ?>
                  <p><strong>Est. Delivery:</strong> <?= date('M j, Y', strtotime($o['shipment']['estimated_delivery'])) ?></p>
                <?php endif; ?>
                <div class="sc-shipment-actions">
                  <?php
                  $statusOrder = Shipment::STATUSES;
                  $curIdx      = array_search($o['shipment']['status'], $statusOrder);
                  $nextStatus  = ($curIdx !== false && isset($statusOrder[$curIdx + 1]))
                                 ? $statusOrder[$curIdx + 1] : null;
                  $nextLabels  = [
                      'packed'           => '<i class="fa-solid fa-box"></i> Mark as Packed',
                      'shipped'          => '<i class="fa-solid fa-paper-plane"></i> Mark as Shipped',
                      'in_transit'       => '<i class="fa-solid fa-truck"></i> Mark as In Transit',
                      'out_for_delivery' => '<i class="fa-solid fa-truck-fast"></i> Out for Delivery',
                  ];
                  if ($nextStatus && isset($nextLabels[$nextStatus])):
                  ?>
                  <form method="POST" action="seller-orders.php" style="display:inline;">
                    <input type="hidden" name="action" value="quick_status">
                    <input type="hidden" name="shipment_id" value="<?= (int)$o['shipment']['id'] ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($nextStatus) ?>">
                    <button type="submit" class="sc-btn-quick-status">
                      <?= $nextLabels[$nextStatus] ?>
                    </button>
                  </form>
                  <?php endif; ?>
                  <button class="sc-btn-update-shipment" onclick="openUpdateShipment(
                    <?= (int)$o['shipment']['id'] ?>,
                    '<?= htmlspecialchars($o['shipment']['courier'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($o['shipment']['tracking_number'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($o['shipment']['estimated_delivery'] ?? '', ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($o['shipment']['status'], ENT_QUOTES) ?>',
                    '<?= htmlspecialchars($o['shipment']['notes'] ?? '', ENT_QUOTES) ?>'
                  )">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                  </button>
                </div>
              </div>
              <?php else: ?>
              <div class="sc-order-actions">
                <?php
                $suggestedDate = Shipment::calcEstimatedDelivery(
                    $sellerAddress['province'] ?? '',
                    $sellerAddress['city']     ?? '',
                    $o['province']             ?? '',
                    $o['city']                 ?? '',
                    $o['delivery_method']      ?? 'deliver'
                );
                ?>
                <button class="sc-btn-ship" onclick="openCreateShipment(
                  <?= (int)$o['order_id'] ?>,
                  '<?= htmlspecialchars($o['buyer_first_name'] . ' ' . $o['buyer_last_name'], ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($suggestedDate, ENT_QUOTES) ?>'
                )">
                  <i class="fa-solid fa-truck-fast"></i> Create Shipment
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

<!-- ── CREATE SHIPMENT MODAL ── -->
<div class="sc-modal-overlay" id="createShipmentOverlay" style="display:none;" onclick="if(event.target===this) closeModal('createShipmentOverlay')">
  <div class="sc-modal">
    <div class="sc-modal-header">
      <h3><i class="fa-solid fa-truck-fast"></i> Create Shipment</h3>
      <button class="sc-modal-close" onclick="closeModal('createShipmentOverlay')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form method="POST" action="seller-orders.php">
      <input type="hidden" name="action" value="create_shipment">
      <input type="hidden" name="order_id" id="createOrderId">

      <div class="sc-modal-buyer" id="createBuyerName"></div>

      <div class="sc-form-group">
        <label>Courier <span class="sc-required">*</span></label>
        <select name="courier" required class="sc-form-control">
          <option value="">Select courier</option>
          <option value="LBC">LBC</option>
          <option value="J&T Express">J&amp;T Express</option>
          <option value="Ninja Van">Ninja Van</option>
          <option value="JRS Express">JRS Express</option>
          <option value="Flash Express">Flash Express</option>
          <option value="Lalamove">Lalamove</option>
          <option value="Grab Express">Grab Express</option>
          <option value="2GO">2GO</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="sc-form-group">
        <label>Tracking Number <span class="sc-required">*</span></label>
        <div class="sc-input-group">
          <input type="text" name="tracking_number" id="createTrackingNumber"
            placeholder="e.g. LBC123456789" required class="sc-form-control">
          <button type="button" class="sc-btn-generate" onclick="generateTracking()">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Generate
          </button>
        </div>
      </div>

      <div class="sc-form-group">
        <label>Initial Status</label>
        <select name="initial_status" class="sc-form-control">
          <option value="pending">Pending</option>
          <option value="packed">Packed</option>
          <option value="shipped">Shipped</option>
        </select>
      </div>

      <div class="sc-form-group">
        <label>Estimated Delivery Date</label>
        <input type="date" name="estimated_delivery" id="createEstimatedDelivery" class="sc-form-control">
        <p class="sc-form-hint" id="createDeliveryHint"></p>
      </div>

      <div class="sc-form-group">
        <label>Notes (optional)</label>
        <textarea name="notes" rows="2" placeholder="Any notes for this shipment..." class="sc-form-control"></textarea>
      </div>

      <div class="sc-modal-actions">
        <button type="submit" class="sc-btn-primary"><i class="fa-solid fa-truck-fast"></i> Create Shipment</button>
        <button type="button" class="sc-btn-secondary" onclick="closeModal('createShipmentOverlay')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- ── UPDATE SHIPMENT MODAL ── -->
<div class="sc-modal-overlay" id="updateShipmentOverlay" style="display:none;" onclick="if(event.target===this) closeModal('updateShipmentOverlay')">
  <div class="sc-modal">
    <div class="sc-modal-header">
      <h3><i class="fa-solid fa-pen-to-square"></i> Update Shipment</h3>
      <button class="sc-modal-close" onclick="closeModal('updateShipmentOverlay')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form method="POST" action="seller-orders.php">
      <input type="hidden" name="action" value="update_shipment">
      <input type="hidden" name="shipment_id" id="updateShipmentId">

      <div class="sc-form-group">
        <label>Courier <span class="sc-required">*</span></label>
        <select name="courier" id="updateCourier" required class="sc-form-control">
          <option value="">Select courier</option>
          <option value="LBC">LBC</option>
          <option value="J&T Express">J&amp;T Express</option>
          <option value="Ninja Van">Ninja Van</option>
          <option value="JRS Express">JRS Express</option>
          <option value="Flash Express">Flash Express</option>
          <option value="Lalamove">Lalamove</option>
          <option value="Grab Express">Grab Express</option>
          <option value="2GO">2GO</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <div class="sc-form-group">
        <label>Tracking Number <span class="sc-required">*</span></label>
        <input type="text" name="tracking_number" id="updateTrackingNumber"
          placeholder="e.g. LBC123456789" required class="sc-form-control">
      </div>

      <div class="sc-form-group">
        <label>Shipment Status <span class="sc-required">*</span></label>
        <select name="status" id="updateStatus" required class="sc-form-control">
          <option value="pending">Pending</option>
          <option value="packed">Packed</option>
          <option value="shipped">Shipped</option>
          <option value="in_transit">In Transit</option>
          <option value="out_for_delivery">Out for Delivery</option>
          <!-- Delivered is confirmed by the buyer, not set by the seller -->
        </select>
      </div>

      <div class="sc-form-group">
        <label>Estimated Delivery Date</label>
        <input type="date" name="estimated_delivery" id="updateEstimatedDelivery" class="sc-form-control">
      </div>

      <div class="sc-form-group">
        <label>Notes (optional)</label>
        <textarea name="notes" id="updateNotes" rows="2" placeholder="Any notes..." class="sc-form-control"></textarea>
      </div>

      <div class="sc-modal-actions">
        <button type="submit" class="sc-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        <button type="button" class="sc-btn-secondary" onclick="closeModal('updateShipmentOverlay')">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

<script>
function closeModal(id) {
  document.getElementById(id).classList.remove('active');
}

function openCreateShipment(orderId, buyerName, suggestedDate) {
  document.getElementById('createOrderId').value = orderId;
  document.getElementById('createBuyerName').innerHTML =
    '<i class="fa-solid fa-user"></i> Buyer: <strong>' + buyerName + '</strong>';
  document.getElementById('createEstimatedDelivery').value = suggestedDate;
  document.getElementById('createDeliveryHint').textContent =
    suggestedDate ? 'Suggested based on buyer location: ' + formatDate(suggestedDate) : '';
  document.getElementById('createShipmentOverlay').classList.add('active');
}

function openUpdateShipment(shipmentId, courier, trackingNumber, estimatedDelivery, status, notes) {
  document.getElementById('updateShipmentId').value = shipmentId;
  document.getElementById('updateCourier').value = courier;
  document.getElementById('updateTrackingNumber').value = trackingNumber;
  document.getElementById('updateEstimatedDelivery').value = estimatedDelivery;
  document.getElementById('updateStatus').value = status;
  document.getElementById('updateNotes').value = notes;
  document.getElementById('updateShipmentOverlay').classList.add('active');
}

function generateTracking() {
  const now  = new Date();
  const y    = now.getFullYear();
  const m    = String(now.getMonth() + 1).padStart(2, '0');
  const d    = String(now.getDate()).padStart(2, '0');
  const seq  = String(Math.floor(Math.random() * 9000) + 1000);
  document.getElementById('createTrackingNumber').value = `SC-${y}${m}${d}-${seq}`;
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr + 'T00:00:00');
  return d.toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' });
}

document.addEventListener('DOMContentLoaded', function() {
  const alerts = document.querySelectorAll('.sc-alert');
  alerts.forEach(a => setTimeout(() => a.style.opacity = '0', 4000));
});
</script>
</body>
</html>
