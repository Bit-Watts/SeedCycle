<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
  <link rel="stylesheet" href="<?= asset('assets/css/cart.css') ?>">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'marketplace'; require __DIR__ . '/includes/sidebar.php'; ?>

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
          <div class="sc-cart-card-top">
            <div class="sc-cart-emoji">🌱</div>
            <div class="sc-cart-info">
              <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
              <p class="sc-cart-type"><?= htmlspecialchars($item['category'] ?? 'Seed') ?></p>
              <p class="sc-cart-stock">Stock: <?= (int)$item['stock_quantity'] ?> packs</p>
            </div>
          </div>
          <div class="sc-cart-card-bottom">
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

<?php require __DIR__ . '/includes/footer.php'; ?>

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
    // Ensure price is parsed as float to avoid string multiplication issues
    const price = parseFloat(prices[cartId]);
    const sub = price * qty;
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
      // Remove peso sign and commas, then parse as float
      const cleanPrice = el.textContent.replace('₱', '').replace(/,/g, '');
      total += parseFloat(cleanPrice) || 0;
    });
    document.getElementById('grand-total').textContent = '₱' + total.toFixed(2);
  }
</script>


<?php require __DIR__ . '/includes/logout-modal.php'; ?>
</body>
</html>