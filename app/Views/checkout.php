<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Checkout</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/cart.css">
  <link rel="stylesheet" href="assets/css/checkout.css">
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

<?php if (empty($cartItems)): ?>
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
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>
  <main class="sc-main" style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
    <span style="font-size:48px;">🛒</span>
    <p style="font-size:16px; color:#888; margin:16px 0;">Your cart is empty.</p>
    <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
  </main>
</div>
<?php else: ?>

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
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-checkout-layout">

  <!-- FORM -->
  <div class="sc-checkout-form-wrap">

    <div style="display:flex; align-items:center; justify-content:space-between;">
      <h1 style="font-family:'Poppins',sans-serif; font-size:24px; font-weight:700; color:#2E7D32;">Checkout</h1>
      <a href="cart.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Back to Cart</a>
    </div>

    <?php if ($error): ?>
      <div class="sc-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="checkout.php">

      <!-- DELIVERY ADDRESS -->
      <div class="sc-checkout-section">
        <h2>📍 Delivery Address</h2>

        <div class="sc-form-group">
          <label>Street Address (optional)</label>
          <input type="text" name="street_address" placeholder="House no., street name"
                 value="<?= htmlspecialchars($_POST['street_address'] ?? $user['address'] ?? '') ?>">
        </div>

        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Barangay <span style="color:#e53935;">*</span></label>
            <input type="text" name="barangay" required placeholder="Barangay"
                   value="<?= htmlspecialchars($_POST['barangay'] ?? '') ?>">
          </div>
          <div class="sc-form-group">
            <label>City <span style="color:#e53935;">*</span></label>
            <input type="text" name="city" required placeholder="City"
                   value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
          </div>
        </div>

        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Municipality <span style="color:#e53935;">*</span></label>
            <input type="text" name="municipality" required placeholder="Municipality"
                   value="<?= htmlspecialchars($_POST['municipality'] ?? '') ?>">
          </div>
          <div class="sc-form-group">
            <label>Province <span style="color:#e53935;">*</span></label>
            <input type="text" name="province" required placeholder="Province"
                   value="<?= htmlspecialchars($_POST['province'] ?? '') ?>">
          </div>
        </div>

        <div class="sc-form-group" style="max-width:200px;">
          <label>ZIP Code <span style="color:#e53935;">*</span></label>
          <input type="text" name="zip_code" required placeholder="e.g. 1234"
                 value="<?= htmlspecialchars($_POST['zip_code'] ?? '') ?>">
        </div>
      </div>

      <!-- DELIVERY METHOD -->
      <div class="sc-checkout-section">
        <h2>🚚 Delivery Method</h2>
        <div class="sc-delivery-options">
          <label class="sc-delivery-option" onclick="this.classList.add('selected'); document.querySelectorAll('.sc-delivery-option').forEach(o=>{ if(o!==this) o.classList.remove('selected'); })">
            <input type="radio" name="delivery_method" value="standard" required
                   <?= (($_POST['delivery_method'] ?? '') === 'standard') ? 'checked' : '' ?>>
            <div class="sc-delivery-option-title">📦 Standard</div>
            <div class="sc-delivery-option-desc">5–7 business days</div>
          </label>
          <label class="sc-delivery-option" onclick="this.classList.add('selected'); document.querySelectorAll('.sc-delivery-option').forEach(o=>{ if(o!==this) o.classList.remove('selected'); })">
            <input type="radio" name="delivery_method" value="express"
                   <?= (($_POST['delivery_method'] ?? '') === 'express') ? 'checked' : '' ?>>
            <div class="sc-delivery-option-title">⚡ Express</div>
            <div class="sc-delivery-option-desc">2–3 business days</div>
          </label>
          <label class="sc-delivery-option" onclick="this.classList.add('selected'); document.querySelectorAll('.sc-delivery-option').forEach(o=>{ if(o!==this) o.classList.remove('selected'); })">
            <input type="radio" name="delivery_method" value="pickup"
                   <?= (($_POST['delivery_method'] ?? '') === 'pickup') ? 'checked' : '' ?>>
            <div class="sc-delivery-option-title">🏪 Pickup</div>
            <div class="sc-delivery-option-desc">Pick up at our location</div>
          </label>
        </div>
      </div>

      <!-- SUBMIT (hidden, triggered from summary) -->
      <button type="submit" id="place-order-btn" style="display:none;"></button>
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

      <button class="sc-btn-place-order" onclick="document.querySelector('form').submit()">
        Place Order →
      </button>
      <p class="sc-checkout-note">By placing your order, you agree to our terms of service.</p>
    </div>
  </div>

</div><!-- end sc-checkout-layout -->

  </main>
</div><!-- end sc-dashboard -->

<?php endif; ?>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  // Highlight selected delivery option on page load if POST
  document.querySelectorAll('.sc-delivery-option input[type="radio"]').forEach(radio => {
    if (radio.checked) {
      radio.closest('.sc-delivery-option').classList.add('selected');
    }
  });
</script>


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
