<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Checkout</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/checkout.css') ?>">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<?php if (empty($cartItems)): ?>
<div class="sc-dashboard">
  <?php $activePage = 'marketplace'; require __DIR__ . '/includes/sidebar.php'; ?>
  <main class="sc-main">
    <div class="sc-empty-state">
      <i class="fa-solid fa-cart-shopping"></i>
      <p>Your cart is empty.</p>
      <a href="marketplace.php" class="sc-btn sc-btn-green">Browse Seeds</a>
    </div>
  </main>
</div>
<?php else: ?>

<div class="sc-dashboard">

  <?php $activePage = 'marketplace'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">
    <div class="sc-checkout-header">
      <h1><i class="fa-solid fa-bag-shopping"></i> Checkout</h1>
      <a href="cart.php" class="sc-back-link"><i class="fa-solid fa-arrow-left"></i> Back to Cart</a>
    </div>

    <?php if ($error): ?>
      <div class="sc-error">
        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="sc-checkout-layout">
      <div class="sc-checkout-form">
        <form method="POST" action="checkout.php" id="checkout-form">

          <!-- DELIVERY ADDRESS -->
          <div class="sc-checkout-section">
            <h2><i class="fa-solid fa-location-dot"></i> Delivery Address</h2>

            <?php if (!empty($user['address'])): ?>
            <div class="sc-use-profile-address" onclick="useProfileAddress()">
              <i class="fa-solid fa-user"></i> Use my saved address
            </div>
            <?php endif; ?>

            <div class="sc-form-group">
              <label>Street Address (optional)</label>
              <input type="text" name="street_address" id="street_address" placeholder="House no., street name"
                     value="<?= htmlspecialchars($_POST['street_address'] ?? '') ?>">
            </div>

            <div class="sc-form-row">
              <div class="sc-form-group">
                <label>Barangay <span class="sc-required">*</span></label>
                <input type="text" name="barangay" id="barangay" required placeholder="Barangay"
                       value="<?= htmlspecialchars($_POST['barangay'] ?? '') ?>">
              </div>
              <div class="sc-form-group">
                <label>City <span class="sc-required">*</span></label>
                <input type="text" name="city" id="city" required placeholder="City"
                       value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
              </div>
            </div>

            <div class="sc-form-row">
              <div class="sc-form-group">
                <label>Province <span class="sc-required">*</span></label>
                <input type="text" name="province" id="province" required placeholder="Province"
                       value="<?= htmlspecialchars($_POST['province'] ?? '') ?>">
              </div>
              <div class="sc-form-group" >
                <label>ZIP Code <span class="sc-required">*</span></label>
                <input type="text" name="zip_code" id="zip_code" required placeholder="e.g. 1234"
                       value="<?= htmlspecialchars($_POST['zip_code'] ?? '') ?>">
              </div>
            </div>


          </div>

          <!-- DELIVERY METHOD -->
          <div class="sc-checkout-section">
            <h2><i class="fa-solid fa-truck-fast"></i> Delivery Method</h2>
            <div class="sc-delivery-options">
              <label class="sc-delivery-option <?= (($_POST['delivery_method'] ?? '') === 'standard') ? 'selected' : '' ?>" 
                     onclick="selectDelivery(this)">
                <input type="radio" name="delivery_method" value="standard" required
                       <?= (($_POST['delivery_method'] ?? '') === 'standard') ? 'checked' : '' ?>>
                <div class="sc-delivery-option-title">📦 Standard</div>
                <div class="sc-delivery-option-desc">5–7 business days</div>
              </label>
              <label class="sc-delivery-option <?= (($_POST['delivery_method'] ?? '') === 'express') ? 'selected' : '' ?>" 
                     onclick="selectDelivery(this)">
                <input type="radio" name="delivery_method" value="express"
                       <?= (($_POST['delivery_method'] ?? '') === 'express') ? 'checked' : '' ?>>
                <div class="sc-delivery-option-title">⚡ Express</div>
                <div class="sc-delivery-option-desc">2–3 business days</div>
              </label>
              <label class="sc-delivery-option <?= (($_POST['delivery_method'] ?? '') === 'pickup') ? 'selected' : '' ?>" 
                     onclick="selectDelivery(this)">
                <input type="radio" name="delivery_method" value="pickup"
                       <?= (($_POST['delivery_method'] ?? '') === 'pickup') ? 'checked' : '' ?>>
                <div class="sc-delivery-option-title">🏪 Pickup</div>
                <div class="sc-delivery-option-desc">Pick up at location</div>
              </label>
            </div>
          </div>

          <!-- PAYMENT METHOD -->
          <div class="sc-checkout-section">
            <h2><i class="fa-solid fa-credit-card"></i> Payment Method</h2>
            <div class="sc-payment-options">
              <label class="sc-payment-option selected" onclick="selectPayment(this)">
                <input type="radio" name="payment_method" value="cod" checked required>
                <div class="sc-payment-icon">💵</div>
                <div class="sc-payment-title">Cash on Delivery</div>
                <div class="sc-payment-desc">Pay when you receive</div>
              </label>
              <label class="sc-payment-option" onclick="selectPayment(this)">
                <input type="radio" name="payment_method" value="gcash" required>
                <div class="sc-payment-icon">📱</div>
                <div class="sc-payment-title">GCash</div>
                <div class="sc-payment-desc">Pay securely online</div>
              </label>
            </div>
            <!-- GCash coming soon notice -->
            <div id="gcash-notice" style="display:none; margin-top:10px; padding:10px 14px; background:#fff8e1; border:1px solid #ffe082; border-radius:10px; font-size:13px; color:#f57f17;">
              <i class="fa-solid fa-triangle-exclamation"></i> GCash online payment is currently unavailable. Please use Cash on Delivery.
            </div>
          </div>

        </form>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-checkout-summary">
        <div class="sc-checkout-summary-card">
          <h2>Order Summary</h2>

          <?php foreach ($cartItems as $item): ?>
          <div class="sc-order-item-line">
            <div>
              <div class="sc-order-item-name">🌱 <?= htmlspecialchars($item['name']) ?></div>
              <div class="sc-order-item-qty">× <?= (int)$item['quantity'] ?> pack<?= $item['quantity'] > 1 ? 's' : '' ?></div>
            </div>
            <div class="sc-order-item-price">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
          </div>
          <?php endforeach; ?>

          <div class="sc-order-total-row">
            <span class="sc-order-total-label">Total</span>
            <span class="sc-order-total-value">₱<?= number_format($total, 2) ?></span>
          </div>

          <button type="button" class="sc-btn-place-order">
            <i class="fa-solid fa-check-circle"></i> Place Order
          </button>
          <p class="sc-checkout-note">By placing your order, you agree to our terms of service.</p>
        </div>
      </div>
    </div>

  </main>
</div><!-- end sc-dashboard -->

<?php endif; ?>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  // Profile address data
  const profileAddress = <?= json_encode($user['address'] ?? '') ?>;

  // Use profile address
  function useProfileAddress() {
    if (!profileAddress) return;
    
    // Parse Philippine address format: "Street/Purok, Brgy. Name, City, Province ZIP"
    // Example: "Prk. Sto. Domingo, Brgy. Handumanan, Bacolod City, Negros Occidental 6100"
    const parts = profileAddress.split(',').map(s => s.trim());
    
    if (parts.length >= 3) {
      // First part: Street/Purok
      document.getElementById('street_address').value = parts[0] || '';
      
      // Second part: Barangay (remove "Brgy." or "Barangay" prefix if present)
      let barangay = parts[1] || '';
      barangay = barangay.replace(/^(Brgy\.?|Barangay)\s*/i, '');
      document.getElementById('barangay').value = barangay;
      
      // Third part: City
      document.getElementById('city').value = parts[2] || '';
      
      // Fourth part (if exists): Province and ZIP
      if (parts.length >= 4) {
        const lastPart = parts[3].trim();
        // Try to extract ZIP code (usually 4 digits at the end)
        const zipMatch = lastPart.match(/\b(\d{4})\b$/);
        
        if (zipMatch) {
          const zip = zipMatch[1];
          const province = lastPart.replace(/\s*\d{4}\s*$/, '').trim();
          document.getElementById('province').value = province;
          document.getElementById('zip_code').value = zip;
        } else {
          document.getElementById('province').value = lastPart;
        }
      }
    } else if (parts.length === 2) {
      // Format: "Barangay, City"
      let barangay = parts[0] || '';
      barangay = barangay.replace(/^(Brgy\.?|Barangay)\s*/i, '');
      document.getElementById('barangay').value = barangay;
      document.getElementById('city').value = parts[1] || '';
    } else {
      // If format doesn't match, just put it in street address
      document.getElementById('street_address').value = profileAddress;
    }
  }

  // Select delivery method
  function selectDelivery(element) {
    document.querySelectorAll('.sc-delivery-option').forEach(opt => {
      opt.classList.remove('selected');
    });
    element.classList.add('selected');
    element.querySelector('input[type="radio"]').checked = true;
  }

  // Select payment method
  function selectPayment(element) {
    document.querySelectorAll('.sc-payment-option').forEach(opt => {
      opt.classList.remove('selected');
    });
    element.classList.add('selected');
    const radio = element.querySelector('input[type="radio"]');
    radio.checked = true;

    // Show/hide GCash notice
    const notice = document.getElementById('gcash-notice');
    if (radio.value === 'gcash') {
      notice.style.display = 'block';
    } else {
      notice.style.display = 'none';
    }
  }

  // Intercept Place Order if GCash is selected
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.sc-btn-place-order').addEventListener('click', function(e) {
      const selected = document.querySelector('input[name="payment_method"]:checked');
      if (selected && selected.value === 'gcash') {
        e.preventDefault();
        document.getElementById('gcashComingSoonModal').style.display = 'flex';
        return;
      }
      document.getElementById('checkout-form').submit();
    });
  });

  // Highlight selected options on page load
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sc-delivery-option input[type="radio"]:checked').forEach(radio => {
      radio.closest('.sc-delivery-option').classList.add('selected');
    });
    
    document.querySelectorAll('.sc-payment-option input[type="radio"]:checked').forEach(radio => {
      radio.closest('.sc-payment-option').classList.add('selected');
    });
  });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

<!-- GCash Coming Soon Modal -->
<div id="gcashComingSoonModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);
     z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:20px; padding:36px 32px; max-width:380px; width:90%;
              text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
    <div style="font-size:52px; margin-bottom:12px;">🚧</div>
    <h2 style="font-family:'Poppins',sans-serif; color:#2E7D32; font-size:20px; margin-bottom:10px;">
      Online Payment Coming Soon
    </h2>
    <p style="font-size:14px; color:#666; line-height:1.6; margin-bottom:24px;">
      GCash payment is still in the works and not available yet.<br>
      Please use <strong>Cash on Delivery</strong> for now.
    </p>
    <button onclick="switchToCOD()"
      style="width:100%; background:#4CAF50; color:#fff; border:none; padding:13px;
             border-radius:12px; font-size:15px; font-weight:600; cursor:pointer;
             font-family:'Poppins',sans-serif; margin-bottom:10px;">
      Switch to Cash on Delivery
    </button>
    <button onclick="document.getElementById('gcashComingSoonModal').style.display='none'"
      style="width:100%; background:#f5f5f5; color:#666; border:none; padding:11px;
             border-radius:10px; font-size:14px; cursor:pointer; font-family:'Inter',sans-serif;">
      Go Back
    </button>
  </div>
</div>

<script>
function switchToCOD() {
  // Unselect GCash, select COD
  document.querySelectorAll('.sc-payment-option').forEach(opt => opt.classList.remove('selected'));
  const codRadio = document.querySelector('input[name="payment_method"][value="cod"]');
  codRadio.checked = true;
  codRadio.closest('.sc-payment-option').classList.add('selected');
  document.getElementById('gcash-notice').style.display = 'none';
  document.getElementById('gcashComingSoonModal').style.display = 'none';
}
</script>
</body>
</html>
