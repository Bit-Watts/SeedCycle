<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/cart.css">
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image']??"";?><?php if(!empty($pi)):?><img src="<?=htmlspecialchars($pi)?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><?php else:?>🌱<?php endif;?></div>
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image'] ?? ''; ?>
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image']??"";?><?php if(!empty($pi)):?><img src="<?=htmlspecialchars($pi)?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><?php else:?>🌱<?php endif;?></div>
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
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

    <div class="sc-main-header" style="display:flex; align-items:center; justify-content:space-between;">
      <div>
        <h1>Your Cart</h1>
        <p>Review your items before checkout.</p>
      </div>
      <a href="marketplace.php" style="font-size:13px; color:#4CAF50; text-decoration:none; font-weight:500;">← Continue Shopping</a>
    </div>

    <?php if (empty($cartItems)): ?>
      <div class="sc-section" style="text-align:center; padding:60px 20px;">
        <div style="font-size:48px; margin-bottom:16px;">🛒</div>
        <p style="color:#888; margin-bottom:16px;">Your cart is empty.</p>
        <a href="marketplace.php"><button class="sc-btn-shop" style="background:#4CAF50; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:14px; cursor:pointer;">Browse Seeds</button></a>
      </div>
    <?php else: ?>

    <div class="sc-cart-layout">

      <!-- CART ITEMS -->
      <div class="sc-cart-items" id="cart-items-container">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
          <div class="sc-cart-emoji">🌱</div>
          <div class="sc-cart-info">
            <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
            <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
            <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
          </div>
          <div class="sc-cart-qty">
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, -1)">−</button>
            <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span>
            <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, <?= (int)$item['inventory_id'] ?>, 1, <?= (int)$item['stock_quantity'] ?>)">+</button>
          </div>
          <div class="sc-cart-price-wrap">
            <span class="sc-cart-unit">₱<?= number_format($item['price'], 2) ?>/pack</span>
            <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)" title="Remove">✕</button>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ORDER SUMMARY -->
      <div class="sc-cart-summary">
        <h2>Order Summary</h2>
        <div class="sc-summary-lines" id="summary-lines">
          <?php foreach ($cartItems as $item): ?>
          <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
            <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= (int)$item['quantity'] ?></span></span>
            <span id="ssub-<?= $item['id'] ?>">₱<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="sc-summary-divider"></div>
        <div class="sc-summary-total">
          <span>Total</span>
          <span id="grand-total">₱<?= number_format($total, 2) ?></span>
        </div>
        <a href="checkout.php">
          <button class="sc-btn-checkout">Proceed to Checkout →</button>
        </a>
        <p class="sc-summary-note">Prices are per pack. Delivery fees applied at checkout.</p>
      </div>

    </div>
    <?php endif; ?>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= (float)$item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(cartId, inventoryId, delta, maxStock = 9999) {
    const qtyEl  = document.getElementById('qty-' + cartId);
    const sqtyEl = document.getElementById('sqty-' + cartId);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1)        qty = 1;
    if (qty > maxStock) qty = maxStock;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[cartId] * qty;
    document.getElementById('sub-' + cartId).textContent  = '₱' + sub.toFixed(2);
    document.getElementById('ssub-' + cartId).textContent = '₱' + sub.toFixed(2);
    updateTotal();
    fetch('cart-update.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}&quantity=${qty}`
    });
  }

  function removeItem(cartId) {
    fetch('cart-remove.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
      body: `cart_id=${cartId}`
    })
    .then(r => r.json())
    .then(data => {
      document.getElementById('item-' + cartId)?.remove();
      document.getElementById('summary-' + cartId)?.remove();
      delete prices[cartId];
      updateTotal();
      if (data.empty) {
        document.querySelector('.sc-cart-layout').innerHTML =
          `<div style="text-align:center;padding:60px 20px;">
            <div style="font-size:48px;margin-bottom:16px;">🛒</div>
            <p style="color:#888;margin-bottom:16px;">Your cart is empty.</p>
            <a href="marketplace.php"><button style="background:#4CAF50;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-size:14px;cursor:pointer;">Browse Seeds</button></a>
          </div>`;
      }
    });
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseFloat(el.textContent.replace('₱', '')) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
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
