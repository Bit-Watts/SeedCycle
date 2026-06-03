<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Successful - SeedCycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/payment.css') ?>">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-payment-page">
  <div class="sc-payment-card">

    <div class="sc-payment-icon-wrap sc-payment-icon-success">
      <i class="fa-solid fa-check"></i>
    </div>

    <h1 class="sc-payment-title-success">Payment Successful!</h1>
    <p class="sc-payment-message">
      <?php if (($order['payment_method'] ?? 'cod') === 'cod'): ?>
        Your order has been placed. You will pay when you receive your seeds.
      <?php else: ?>
        Your payment has been processed. Your order is now being prepared for shipment.
      <?php endif; ?>
    </p>

    <div class="sc-payment-details">
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Order ID</span>
        <span class="sc-payment-detail-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Order Date</span>
        <span class="sc-payment-detail-value"><?= date('F j, Y', strtotime($order['created_at'])) ?></span>
      </div>
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Payment Method</span>
        <span class="sc-payment-detail-value"><?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'GCash' ?></span>
      </div>
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Total Amount</span>
        <span class="sc-payment-detail-value amount">₱<?= number_format($order['total_amount'], 2) ?></span>
      </div>
    </div>

    <?php if (!empty($order['payment_reference'])): ?>
    <div class="sc-payment-reference">
      <div class="sc-payment-reference-label">Payment Reference Number</div>
      <div class="sc-payment-reference-value"><?= htmlspecialchars($order['payment_reference']) ?></div>
    </div>
    <?php endif; ?>

    <?php if (!empty($orderItems)): ?>
    <div class="sc-payment-items">
      <div class="sc-payment-items-title">Order Items</div>
      <?php foreach ($orderItems as $item): ?>
      <div class="sc-payment-item">
        <div>
          <span class="sc-payment-item-name"><?= htmlspecialchars($item['name'] ?? $item['seed_name'] ?? 'Unknown Item') ?></span>
          <span class="sc-payment-item-qty">×<?= (int)$item['quantity'] ?></span>
        </div>
        <span class="sc-payment-item-price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="sc-payment-actions">
      <a href="orders.php" class="sc-btn sc-btn-primary sc-btn-lg">
        <i class="fa-solid fa-bag-shopping"></i> View My Orders
      </a>
      <a href="marketplace.php" class="sc-btn sc-btn-outline sc-btn-lg">
        <i class="fa-solid fa-store"></i> Continue Shopping
      </a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

</body>
</html>
