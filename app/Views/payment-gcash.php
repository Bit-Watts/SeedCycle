<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GCash Payment - SeedCycle</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #007DFF 0%, #0062CC 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .gcash-container {
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      max-width: 420px;
      width: 100%;
      overflow: hidden;
    }

    .gcash-header {
      background: linear-gradient(135deg, #007DFF 0%, #0062CC 100%);
      padding: 24px;
      text-align: center;
      color: #ffffff;
    }

    .gcash-logo {
      font-size: 48px;
      margin-bottom: 8px;
    }

    .gcash-title {
      font-family: 'Poppins', sans-serif;
      font-size: 24px;
      font-weight: 700;
      margin-bottom: 4px;
    }

    .gcash-subtitle {
      font-size: 13px;
      opacity: 0.9;
    }

    .gcash-body {
      padding: 32px 28px;
    }

    .payment-amount {
      text-align: center;
      margin-bottom: 32px;
    }

    .amount-label {
      font-size: 13px;
      color: #888;
      margin-bottom: 8px;
    }

    .amount-value {
      font-family: 'Poppins', sans-serif;
      font-size: 42px;
      font-weight: 700;
      color: #2E7D32;
    }

    .payment-details {
      background: #F5FBF0;
      border: 1px solid #c8e6c9;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 24px;
    }

    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      font-size: 14px;
    }

    .detail-label {
      color: #666;
    }

    .detail-value {
      font-weight: 600;
      color: #333;
    }

    .payment-info {
      background: #FFF8E1;
      border-left: 4px solid #FBC02D;
      padding: 14px 16px;
      border-radius: 8px;
      margin-bottom: 24px;
      font-size: 13px;
      color: #666;
    }

    .payment-info i {
      color: #FBC02D;
      margin-right: 8px;
    }

    .btn-pay {
      width: 100%;
      background: linear-gradient(135deg, #007DFF 0%, #0062CC 100%);
      color: #ffffff;
      font-family: 'Poppins', sans-serif;
      font-size: 16px;
      font-weight: 600;
      padding: 16px;
      border: none;
      border-radius: 12px;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      box-shadow: 0 4px 12px rgba(0,125,255,0.3);
    }

    .btn-pay:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0,125,255,0.4);
    }

    .btn-pay:active {
      transform: translateY(0);
    }

    .btn-pay:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-cancel {
      width: 100%;
      background: #f5f5f5;
      color: #666;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      font-weight: 500;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-top: 12px;
      transition: background 0.2s;
    }

    .btn-cancel:hover {
      background: #e0e0e0;
    }

    .processing-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.8);
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }

    .processing-overlay.active {
      display: flex;
    }

    .processing-card {
      background: #ffffff;
      border-radius: 16px;
      padding: 40px;
      text-align: center;
      max-width: 320px;
    }

    .spinner {
      width: 60px;
      height: 60px;
      border: 4px solid #e0e0e0;
      border-top-color: #007DFF;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .processing-text {
      font-family: 'Poppins', sans-serif;
      font-size: 18px;
      font-weight: 600;
      color: #333;
      margin-bottom: 8px;
    }

    .processing-subtext {
      font-size: 13px;
      color: #888;
    }

    @media (max-width: 480px) {
      .gcash-container {
        border-radius: 0;
      }
      .amount-value {
        font-size: 36px;
      }
    }
  </style>
</head>
<body>

<div class="gcash-container">
  <div class="gcash-header">
    <div class="gcash-logo">📱</div>
    <div class="gcash-title">GCash Payment</div>
    <div class="gcash-subtitle">Secure & Fast Payment</div>
  </div>

  <div class="gcash-body">
    <div class="payment-amount">
      <div class="amount-label">Amount to Pay</div>
      <div class="amount-value">₱<?= number_format($amount, 2) ?></div>
    </div>

    <div class="payment-details">
      <div class="detail-row">
        <span class="detail-label">Order ID</span>
        <span class="detail-value">#<?= $orderId ?></span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Merchant</span>
        <span class="detail-value">SeedCycle</span>
      </div>
      <div class="detail-row">
        <span class="detail-label">Payment Method</span>
        <span class="detail-value">GCash</span>
      </div>
    </div>

    <div class="payment-info">
      <i class="fa-solid fa-circle-info"></i>
      This is a simulated payment. Your order will be processed immediately.
    </div>

    <button class="btn-pay" id="payBtn" onclick="processPayment()">
      <i class="fa-solid fa-lock"></i> Pay Now
    </button>

    <button class="btn-cancel" onclick="cancelPayment()">
      Cancel Payment
    </button>
  </div>
</div>

<div class="processing-overlay" id="processingOverlay">
  <div class="processing-card">
    <div class="spinner"></div>
    <div class="processing-text">Processing Payment</div>
    <div class="processing-subtext">Please wait...</div>
  </div>
</div>

<script>
  const orderId = <?= $orderId ?>;
  const amount = <?= $amount ?>;

  function processPayment() {
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
    
    document.getElementById('processingOverlay').classList.add('active');

    // Send payment request
    const formData = new FormData();
    formData.append('order_id', orderId);
    formData.append('amount', amount);

    fetch('payment-process.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Payment successful
        window.location.href = 'payment-success.php?order_id=' + orderId + '&reference=' + data.reference + '&method=gcash';
      } else {
        // Payment failed
        window.location.href = 'payment-failed.php?order_id=' + orderId + '&message=' + encodeURIComponent(data.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      window.location.href = 'payment-failed.php?order_id=' + orderId + '&message=Network+error';
    });
  }

  function cancelPayment() {
    if (confirm('Are you sure you want to cancel this payment?')) {
      window.location.href = 'checkout.php';
    }
  }
</script>

</body>
</html>
