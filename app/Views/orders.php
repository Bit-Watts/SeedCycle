<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - My Orders</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/orders.css') ?>">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'orders'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1><i class="fa-solid fa-bag-shopping"></i> My Orders</h1>
      <p>Seeds you've purchased from the marketplace.</p>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchase History</h2>
        <span style="font-size:12px; color:#888;"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($orders)): ?>
        <div style="text-align:center; padding:60px 20px; color:#888;">
          <i class="fa-solid fa-box-open" style="font-size:48px; color:#c8e6c9; margin-bottom:16px; display:block;"></i>
          <p style="font-size:14px; margin-bottom:12px;">No orders yet.</p>
          <a href="marketplace.php" style="color:#2E7D32; font-weight:500;">Browse the marketplace &rarr;</a>
        </div>
      <?php else: ?>
        <div class="sc-orders-accordion">
          <?php foreach ($orders as $o):
            $isCancelled = $o['status'] === 'cancelled';
            $shipStatus  = $o['shipping_status'] ?? $o['status'];
            $paymentStatus = $o['payment_status'] ?? 'pending';
            $paymentMethod = $o['payment_method'] ?? 'cod';
          ?>
          <div class="sc-order-accordion-item <?= $isCancelled ? 'sc-order-cancelled' : '' ?>">
            <!-- Order Header (Clickable) -->
            <div class="sc-order-accordion-header" onclick="toggleOrder(this)">
              <div class="sc-order-accordion-left">
                <div class="sc-order-accordion-id">
                  <i class="fa-solid fa-receipt"></i>
                  <span>Order #<?= (int)$o['id'] ?></span>
                </div>
                <div class="sc-order-accordion-date">
                  <i class="fa-regular fa-calendar"></i>
                  <?= date('M j, Y', strtotime($o['created_at'])) ?>
                </div>
              </div>
              
              <div class="sc-order-accordion-center">
                <div class="sc-order-accordion-seeds">
                  <i class="fa-solid fa-seedling"></i>
                  <?= htmlspecialchars($o['seed_names']) ?>
                </div>
              </div>

              <div class="sc-order-accordion-right">
                <div class="sc-order-accordion-badges">
                  <span class="sc-payment-badge sc-payment-<?= htmlspecialchars($paymentStatus) ?>" title="<?= htmlspecialchars(ucfirst($paymentMethod)) ?>">
                    <?php if ($paymentStatus === 'paid'): ?>
                      <i class="fa-solid fa-circle-check"></i> Paid
                    <?php elseif ($paymentStatus === 'cod'): ?>
                      <i class="fa-solid fa-money-bill"></i> COD
                    <?php elseif ($paymentStatus === 'failed'): ?>
                      <i class="fa-solid fa-circle-xmark"></i> Failed
                    <?php else: ?>
                      <i class="fa-solid fa-clock"></i> Pending
                    <?php endif; ?>
                  </span>
                  <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipStatus) ?>">
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipStatus))) ?>
                  </span>
                </div>
                <div class="sc-order-accordion-total">
                  ₱<?= number_format($o['total_amount'], 2) ?>
                </div>
                <div class="sc-order-accordion-toggle">
                  <i class="fa-solid fa-chevron-down"></i>
                </div>
              </div>
            </div>

            <!-- Order Details (Collapsible) -->
            <div class="sc-order-accordion-body">
              <div class="sc-order-details-grid">
                <!-- Order Information -->
                <div class="sc-order-detail-section">
                  <h4><i class="fa-solid fa-info-circle"></i> Order Information</h4>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Order ID:</span>
                    <span class="sc-detail-value">#<?= (int)$o['id'] ?></span>
                  </div>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Order Date:</span>
                    <span class="sc-detail-value"><?= date('F j, Y g:i A', strtotime($o['created_at'])) ?></span>
                  </div>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Delivery Method:</span>
                    <span class="sc-detail-value"><?= htmlspecialchars(ucfirst($o['delivery_method'] ?? 'Standard')) ?></span>
                  </div>
                </div>

                <!-- Payment Information -->
                <div class="sc-order-detail-section">
                  <h4><i class="fa-solid fa-credit-card"></i> Payment</h4>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Method:</span>
                    <span class="sc-detail-value"><?= $paymentMethod === 'cod' ? 'Cash on Delivery' : 'GCash' ?></span>
                  </div>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Status:</span>
                    <span class="sc-detail-value">
                      <span class="sc-payment-badge sc-payment-<?= htmlspecialchars($paymentStatus) ?>">
                        <?= htmlspecialchars(ucfirst($paymentStatus)) ?>
                      </span>
                    </span>
                  </div>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Total Amount:</span>
                    <span class="sc-detail-value sc-detail-amount">₱<?= number_format($o['total_amount'], 2) ?></span>
                  </div>
                </div>

                <!-- Shipping Status -->
                <div class="sc-order-detail-section">
                  <h4><i class="fa-solid fa-truck"></i> Shipping</h4>
                  <div class="sc-order-detail-item">
                    <span class="sc-detail-label">Status:</span>
                    <span class="sc-detail-value">
                      <span class="sc-status-badge sc-status-<?= htmlspecialchars($shipStatus) ?>">
                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $shipStatus))) ?>
                      </span>
                    </span>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="sc-order-actions">
                <?php if (!$isCancelled): ?>
                  <?php if ($paymentStatus === 'pending' || $paymentStatus === 'failed'): ?>
                    <a href="payment-gcash.php?order_id=<?= (int)$o['id'] ?>&amount=<?= $o['total_amount'] ?>" class="sc-btn-action sc-btn-pay">
                      <i class="fa-solid fa-credit-card"></i> Pay Now
                    </a>
                  <?php endif; ?>
                  <?php if ($shipStatus === 'out_for_delivery'): ?>
                    <button class="sc-btn-action sc-btn-confirm-receipt-list"
                            onclick="openConfirmModal(<?= (int)$o['id'] ?>)">
                      <i class="fa-solid fa-box-circle-check"></i> Confirm Receipt
                    </button>
                  <?php endif; ?>
                  <a href="order-tracking.php?id=<?= (int)$o['id'] ?>" class="sc-btn-action sc-btn-track">
                    <i class="fa-solid fa-location-crosshairs"></i> Track Order
                  </a>
                <?php else: ?>
                  <form method="POST" action="orders.php" style="display:inline;"
                        onsubmit="return confirm('Remove this cancelled order from your list?')">
                    <input type="hidden" name="remove_order" value="1">
                    <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                    <button type="submit" class="sc-btn-action sc-btn-remove">
                      <i class="fa-solid fa-trash"></i> Remove Order
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

<!-- CONFIRM RECEIPT MODAL -->
<div id="confirmReceiptModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.55); z-index:9999; align-items:center; justify-content:center; padding:20px;">
  <div style="background:#fff; border-radius:16px; max-width:420px; width:100%; padding:32px 28px; box-shadow:0 8px 32px rgba(0,0,0,0.18); text-align:center;">
    <div style="width:64px; height:64px; background:#e8f5e9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; color:#2E7D32;">
      <i class="fa-solid fa-box-circle-check"></i>
    </div>
    <h3 style="font-family:'Poppins',sans-serif; font-size:18px; font-weight:700; color:#1b5e20; margin-bottom:8px;">Confirm Package Receipt</h3>
    <p style="font-size:13px; color:#555; line-height:1.6; margin-bottom:24px;">
      Please confirm only if you have <strong>physically received</strong> your package. This action cannot be undone.
    </p>
    <div style="display:flex; gap:12px; justify-content:center;">
      <form method="POST" action="confirm-receipt.php" id="confirmReceiptForm">
        <input type="hidden" name="order_id" id="confirmOrderId" value="">
        <button type="submit" style="background:#2E7D32; color:#fff; border:none; padding:12px 28px; border-radius:10px; font-size:14px; font-weight:700; font-family:'Poppins',sans-serif; cursor:pointer;">
          <i class="fa-solid fa-check"></i> Yes, I Received It
        </button>
      </form>
      <button onclick="document.getElementById('confirmReceiptModal').style.display='none'"
              style="background:#f5f5f5; color:#555; border:none; padding:12px 24px; border-radius:10px; font-size:14px; font-weight:600; font-family:'Poppins',sans-serif; cursor:pointer;">
        Cancel
      </button>
    </div>
  </div>
</div>

<style>
.sc-btn-confirm-receipt-list {
  background: linear-gradient(135deg, #2E7D32, #388e3c);
  color: #fff;
  box-shadow: 0 2px 8px rgba(46,125,50,0.25);
}
.sc-btn-confirm-receipt-list:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(46,125,50,0.35);
}
</style>

<script>
function openConfirmModal(orderId) {
  document.getElementById('confirmOrderId').value = orderId;
  document.getElementById('confirmReceiptModal').style.display = 'flex';
}

function toggleOrder(header) {
  const item = header.parentElement;
  const body = item.querySelector('.sc-order-accordion-body');
  const icon = header.querySelector('.sc-order-accordion-toggle i');
  
  // Close all other orders
  document.querySelectorAll('.sc-order-accordion-item').forEach(otherItem => {
    if (otherItem !== item && otherItem.classList.contains('active')) {
      otherItem.classList.remove('active');
      otherItem.querySelector('.sc-order-accordion-body').style.maxHeight = null;
      otherItem.querySelector('.sc-order-accordion-toggle i').style.transform = 'rotate(0deg)';
    }
  });
  
  // Toggle current order
  item.classList.toggle('active');
  
  if (item.classList.contains('active')) {
    body.style.maxHeight = body.scrollHeight + 'px';
    icon.style.transform = 'rotate(180deg)';
  } else {
    body.style.maxHeight = null;
    icon.style.transform = 'rotate(0deg)';
  }
}
</script>

</body>
</html>
