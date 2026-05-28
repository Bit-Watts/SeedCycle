<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Failed - SeedCycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/payment.css">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-payment-page">
  <div class="sc-payment-card">

    <div class="sc-payment-icon-wrap sc-payment-icon-failed">
      <i class="fa-solid fa-xmark"></i>
    </div>

    <h1 class="sc-payment-title-failed">Payment Failed</h1>
    <p class="sc-payment-message">
      We couldn't process your payment. Please try again or use a different payment method.
    </p>

    <div class="sc-payment-error-box">
      <div class="sc-payment-error-title">What happened?</div>
      <div class="sc-payment-error-text">
        <?= htmlspecialchars($_GET['message'] ?? 'The payment transaction could not be completed. This may be due to insufficient funds, network issues, or other technical problems.') ?>
      </div>
    </div>

    <div class="sc-payment-details">
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Order ID</span>
        <span class="sc-payment-detail-value">#<?= (int)$order['id'] ?></span>
      </div>
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Amount</span>
        <span class="sc-payment-detail-value">₱<?= number_format($order['total_amount'], 2) ?></span>
      </div>
      <div class="sc-payment-detail-row">
        <span class="sc-payment-detail-label">Payment Status</span>
        <span class="sc-payment-detail-value failed-status">Failed</span>
      </div>
    </div>

    <div class="sc-payment-actions">
      <a href="payment-gcash.php?order_id=<?= (int)$order['id'] ?>&amount=<?= htmlspecialchars($order['total_amount']) ?>" class="sc-btn sc-btn-green sc-btn-lg">
        <i class="fa-solid fa-rotate-right"></i> Try Again
      </a>
      <a href="orders.php" class="sc-btn sc-btn-ghost sc-btn-lg">
        <i class="fa-solid fa-bag-shopping"></i> View Orders
      </a>
    </div>

    <div class="sc-payment-help">
      Need help? <a href="mailto:support@seedcycle.com">Contact Support</a>
    </div>

  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

</body>
</html>
