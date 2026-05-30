<?php
/**
 * Manage Shipments — seller view of all their shipments with update capability.
 */

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (($_SESSION['role'] ?? 'user') === 'admin') {
    header('Location: admin/dashboard.php');
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Shipment.php';
require_once __DIR__ . '/../app/WebSocket/WebSocketNotifier.php';

global $conn;

$userModel     = new User($conn);
$shipmentModel = new Shipment($conn);
$wsNotifier    = new \App\WebSocket\WebSocketNotifier($conn);

$user    = $userModel->findById($_SESSION['user_id']);
$message = null;
$error   = null;

// ── Handle update shipment POST ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_shipment') {
    $shipmentId        = (int)($_POST['shipment_id']       ?? 0);
    $courier           = trim($_POST['courier']            ?? '');
    $trackingNumber    = trim($_POST['tracking_number']    ?? '');
    $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');
    $status            = trim($_POST['status']             ?? 'pending');
    $notes             = trim($_POST['notes']              ?? '');

    if (!in_array($status, Shipment::STATUSES)) {
        $status = 'pending';
    }

    if ($shipmentId <= 0) {
        $error = 'Invalid shipment.';
    } else {
        // Verify this shipment belongs to the seller
        $stmt = mysqli_prepare($conn,
            'SELECT s.order_id FROM shipments s
             JOIN order_items oi ON oi.order_id = s.order_id
             JOIN seed_listings sl ON sl.inventory_id = oi.inventory_id
             WHERE s.id = ? AND sl.user_id = ? AND sl.status = "approved"
             LIMIT 1'
        );
        mysqli_stmt_bind_param($stmt, 'ii', $shipmentId, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $shipmentOrder = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$shipmentOrder) {
            $error = 'You do not have permission to update this shipment.';
        } else {
            $ok = $shipmentModel->update($shipmentId, $courier, $trackingNumber, $estimatedDelivery, $status, $notes);
            if ($ok) {
                $orderId = (int)$shipmentOrder['order_id'];

                // Sync order status
                if ($status === 'delivered') {
                    $upd = mysqli_prepare($conn,
                        'UPDATE orders
                         SET shipping_status = ?,
                             status = "delivered",
                             payment_status = CASE WHEN payment_method = "cod" THEN "paid" ELSE payment_status END
                         WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($upd, 'si', $status, $orderId);
                } else {
                    $orderStatus = in_array($status, ['pending', 'packed']) ? 'pending' : 'processing';
                    $upd = mysqli_prepare($conn,
                        'UPDATE orders SET shipping_status = ?, status = ? WHERE id = ?'
                    );
                    mysqli_stmt_bind_param($upd, 'ssi', $status, $orderStatus, $orderId);
                }
                mysqli_stmt_execute($upd);
                mysqli_stmt_close($upd);

                $wsNotifier->notifyShipmentUpdate($orderId, $status);
                $message = 'Shipment updated successfully.';
            } else {
                $error = 'Failed to update shipment.';
            }
        }
    }
}

// ── Load all shipments for this seller ──────────────────────────────────────
$shipments = $shipmentModel->getBySellerOrders($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Manage Shipments</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/seller-orders.css">
</head>
<body>

<?php require __DIR__ . '/../app/Views/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'seller-orders'; require __DIR__ . '/../app/Views/includes/sidebar.php'; ?>

  <main class="sc-main">

    <div class="sc-main-header">
      <div>
        <h1><i class="fa-solid fa-truck-fast"></i> Manage Shipments</h1>
        <p>Track and update all shipments for your seed orders.</p>
      </div>
      <a href="seller-orders.php" class="sc-btn sc-btn-outline">
        <i class="fa-solid fa-arrow-left"></i> Back to Orders
      </a>
    </div>

    <?php if ($message): ?>
      <div class="sc-alert sc-alert-success"><i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="sc-alert sc-alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Stats -->
    <?php
    $total     = count($shipments);
    $inTransit = count(array_filter($shipments, fn($s) => in_array($s['status'], ['shipped','in_transit','out_for_delivery'])));
    $delivered = count(array_filter($shipments, fn($s) => $s['status'] === 'delivered'));
    $pending   = count(array_filter($shipments, fn($s) => in_array($s['status'], ['pending','packed'])));
    ?>
    <div class="sc-seller-stats">
      <div class="sc-stat-card">
        <div class="sc-stat-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $total ?></span>
          <span class="sc-stat-label">Total Shipments</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon sc-stat-icon--warn"><i class="fa-solid fa-clock"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $pending ?></span>
          <span class="sc-stat-label">Pending / Packed</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon sc-stat-icon--blue"><i class="fa-solid fa-truck"></i></div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= $inTransit ?></span>
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
        <h2>All Shipments</h2>
        <span style="font-size:12px;color:#888;"><?= $total ?> shipment<?= $total !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($shipments)): ?>
        <div class="sc-seller-empty">
          <i class="fa-solid fa-truck" style="font-size:48px;color:#c8e6c9;margin-bottom:16px;"></i>
          <p>No shipments yet. Create one from <a href="seller-orders.php" style="color:#2E7D32;font-weight:500;">Seller Orders</a>.</p>
        </div>
      <?php else: ?>
        <div class="sc-orders-list">
          <?php foreach ($shipments as $s): ?>
          <div class="sc-order-card">
            <div class="sc-order-header">
              <div class="sc-order-header-left">
                <span class="sc-order-id">Order #<?= (int)$s['order_id'] ?></span>
                <span class="sc-order-date">
                  <i class="fa-regular fa-calendar"></i>
                  <?= date('M j, Y', strtotime($s['order_date'])) ?>
                </span>
              </div>
              <span class="sc-ship-badge sc-ship-<?= htmlspecialchars($s['status']) ?>">
                <?= htmlspecialchars(ucwords(str_replace('_', ' ', $s['status']))) ?>
              </span>
            </div>

            <div class="sc-order-body">
              <div class="sc-order-section">
                <h4><i class="fa-solid fa-user"></i> Buyer</h4>
                <p><?= htmlspecialchars($s['buyer_first_name'] . ' ' . $s['buyer_last_name']) ?></p>
              </div>

              <div class="sc-order-section">
                <h4><i class="fa-solid fa-truck"></i> Shipment Info</h4>
                <p><strong>Courier:</strong> <?= htmlspecialchars($s['courier']) ?></p>
                <p><strong>Tracking #:</strong> <span class="sc-tracking-num"><?= htmlspecialchars($s['tracking_number']) ?></span></p>
                <?php if (!empty($s['estimated_delivery'])): ?>
                  <p><strong>Est. Delivery:</strong> <?= date('M j, Y', strtotime($s['estimated_delivery'])) ?></p>
                <?php endif; ?>
                <?php if (!empty($s['notes'])): ?>
                  <p><strong>Notes:</strong> <?= htmlspecialchars($s['notes']) ?></p>
                <?php endif; ?>
              </div>

              <div class="sc-order-section">
                <h4><i class="fa-solid fa-location-dot"></i> Delivery Address</h4>
                <p>
                  <?php
                  $parts = array_filter([
                    $s['street_address'] ?? '',
                    $s['barangay']       ?? '',
                    $s['city']           ?? '',
                    $s['municipality']   ?? '',
                    $s['province']       ?? '',
                    $s['zip_code']       ?? '',
                  ]);
                  echo htmlspecialchars(implode(', ', $parts));
                  ?>
                </p>
              </div>

              <div class="sc-shipment-actions">
                <button class="sc-btn-update-shipment" onclick="openUpdateShipment(
                  <?= (int)$s['id'] ?>,
                  '<?= htmlspecialchars($s['courier'], ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($s['tracking_number'], ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($s['estimated_delivery'] ?? '', ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($s['status'], ENT_QUOTES) ?>',
                  '<?= htmlspecialchars($s['notes'] ?? '', ENT_QUOTES) ?>'
                )">
                  <i class="fa-solid fa-pen-to-square"></i> Update Shipment
                </button>
                <a href="order-tracking.php?id=<?= (int)$s['order_id'] ?>" class="sc-btn sc-btn-sm sc-btn-outline">
                  <i class="fa-solid fa-location-crosshairs"></i> Track
                </a>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<!-- ── UPDATE SHIPMENT MODAL ── -->
<div class="sc-modal-overlay" id="updateShipmentOverlay" style="display:none;" onclick="if(event.target===this) closeModal()">
  <div class="sc-modal">
    <div class="sc-modal-header">
      <h3><i class="fa-solid fa-pen-to-square"></i> Update Shipment</h3>
      <button class="sc-modal-close" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form method="POST" action="manage-shipments.php">
      <input type="hidden" name="action" value="update_shipment">
      <input type="hidden" name="shipment_id" id="updateShipmentId">

      <div class="sc-form-group">
        <label>Courier <span class="sc-required">*</span></label>
        <select name="courier" id="updateCourier" required class="sc-form-control">
          <option value="">Select courier</option>
          <?php foreach (['LBC','J&T Express','Ninja Van','JRS Express','Flash Express','Lalamove','Grab Express','2GO','Other'] as $c): ?>
            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="sc-form-group">
        <label>Tracking Number <span class="sc-required">*</span></label>
        <input type="text" name="tracking_number" id="updateTrackingNumber" required class="sc-form-control" placeholder="e.g. LBC123456789">
      </div>

      <div class="sc-form-group">
        <label>Shipment Status <span class="sc-required">*</span></label>
        <select name="status" id="updateStatus" required class="sc-form-control">
          <option value="pending">Pending</option>
          <option value="packed">Packed</option>
          <option value="shipped">Shipped</option>
          <option value="in_transit">In Transit</option>
          <option value="out_for_delivery">Out for Delivery</option>
          <option value="delivered">Delivered</option>
        </select>
      </div>

      <div class="sc-form-group">
        <label>Estimated Delivery Date</label>
        <input type="date" name="estimated_delivery" id="updateEstimatedDelivery" class="sc-form-control">
      </div>

      <div class="sc-form-group">
        <label>Notes (optional)</label>
        <textarea name="notes" id="updateNotes" rows="2" class="sc-form-control" placeholder="Any notes..."></textarea>
      </div>

      <div class="sc-modal-actions">
        <button type="submit" class="sc-btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
        <button type="button" class="sc-btn-secondary" onclick="closeModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php require __DIR__ . '/../app/Views/includes/footer.php'; ?>
<?php require __DIR__ . '/../app/Views/includes/logout-modal.php'; ?>

<script>
function openUpdateShipment(id, courier, tracking, estDelivery, status, notes) {
  document.getElementById('updateShipmentId').value        = id;
  document.getElementById('updateCourier').value           = courier;
  document.getElementById('updateTrackingNumber').value    = tracking;
  document.getElementById('updateEstimatedDelivery').value = estDelivery;
  document.getElementById('updateStatus').value            = status;
  document.getElementById('updateNotes').value             = notes;
  document.getElementById('updateShipmentOverlay').style.display = 'flex';
}

function closeModal() {
  document.getElementById('updateShipmentOverlay').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
  const alerts = document.querySelectorAll('.sc-alert');
  alerts.forEach(a => setTimeout(() => a.style.opacity = '0', 4000));
});
</script>
</body>
</html>
