<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/cart.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php">Marketplace</a></li>
    <li><a href="planting-guide.php">Planting Guide</a></li>
  </ul>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?> 👋</span>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-cart-page">

  <div class="sc-cart-header">
    <h1>Your Cart</h1>
    <a href="marketplace.php" class="sc-back-link">← Continue Shopping</a>
  </div>

  <?php
  // Hardcoded cart items — replace with session/DB later
  $cartItems = [
    ['id' => 1, 'emoji' => '🍅', 'name' => 'Tomato Seeds',  'type' => 'Vegetable', 'price' => 45, 'qty' => 2],
    ['id' => 3, 'emoji' => '🌶️', 'name' => 'Chili Seeds',   'type' => 'Vegetable', 'price' => 55, 'qty' => 1],
    ['id' => 4, 'emoji' => '🥬', 'name' => 'Pechay Seeds',  'type' => 'Vegetable', 'price' => 25, 'qty' => 3],
  ];
  $total = array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $cartItems));
  ?>

  <?php if (empty($cartItems)): ?>
    <div class="sc-cart-empty">
      <span>🛒</span>
      <p>Your cart is empty.</p>
      <a href="marketplace.php"><button class="sc-btn-shop">Browse Seeds</button></a>
    </div>
  <?php else: ?>

  <div class="sc-cart-layout">

    <!-- CART ITEMS -->
    <div class="sc-cart-items">
      <?php foreach ($cartItems as $item): ?>
      <div class="sc-cart-card" id="item-<?= $item['id'] ?>">
        <div class="sc-cart-emoji"><?= $item['emoji'] ?></div>
        <div class="sc-cart-info">
          <p class="sc-cart-name"><?= htmlspecialchars($item['name']) ?></p>
          <p class="sc-cart-type"><?= $item['type'] ?></p>
        </div>
        <div class="sc-cart-qty">
          <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, -1)">−</button>
          <span class="sc-qty-val" id="qty-<?= $item['id'] ?>"><?= $item['qty'] ?></span>
          <button class="sc-qty-btn" onclick="changeQty(<?= $item['id'] ?>, 1)">+</button>
        </div>
        <div class="sc-cart-price-wrap">
          <span class="sc-cart-unit">₱<?= $item['price'] ?>/sack</span>
          <span class="sc-cart-subtotal" id="sub-<?= $item['id'] ?>">₱<?= $item['price'] * $item['qty'] ?></span>
        </div>
        <button class="sc-btn-remove" onclick="removeItem(<?= $item['id'] ?>)">✕</button>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="sc-cart-summary">
      <h2>Order Summary</h2>

      <div class="sc-summary-lines">
        <?php foreach ($cartItems as $item): ?>
        <div class="sc-summary-line" id="summary-<?= $item['id'] ?>">
          <span><?= htmlspecialchars($item['name']) ?> × <span id="sqty-<?= $item['id'] ?>"><?= $item['qty'] ?></span></span>
          <span id="ssub-<?= $item['id'] ?>">₱<?= $item['price'] * $item['qty'] ?></span>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="sc-summary-divider"></div>

      <div class="sc-summary-total">
        <span>Total</span>
        <span id="grand-total">₱<?= $total ?></span>
      </div>

      <button class="sc-btn-checkout" onclick="window.location.href='checkout.php'">Proceed to Checkout</button>
      <p class="sc-summary-note">Prices are per sack. Delivery fees applied at checkout.</p>
    </div>

  </div>

  <?php endif; ?>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
  <div class="sc-footer-links">
    <a href="index.php">Home</a>
    <a href="marketplace.php">Marketplace</a>
    <a href="planting-guide.php">Planting Guide</a>
  </div>
</footer>

<script>
  // Hardcoded prices — replace with dynamic data from backend later
  const prices = {
    <?php foreach ($cartItems as $item): ?>
    <?= $item['id'] ?>: <?= $item['price'] ?>,
    <?php endforeach; ?>
  };

  function changeQty(id, delta) {
    const qtyEl  = document.getElementById('qty-' + id);
    const sqtyEl = document.getElementById('sqty-' + id);
    let qty = parseInt(qtyEl.textContent) + delta;
    if (qty < 1) qty = 1;
    qtyEl.textContent  = qty;
    sqtyEl.textContent = qty;
    const sub = prices[id] * qty;
    document.getElementById('sub-' + id).textContent  = '₱' + sub;
    document.getElementById('ssub-' + id).textContent = '₱' + sub;
    updateTotal();
  }

  function removeItem(id) {
    document.getElementById('item-' + id).remove();
    document.getElementById('summary-' + id).remove();
    updateTotal();
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.sc-cart-subtotal').forEach(el => {
      total += parseInt(el.textContent.replace('₱', ''));
    });
    document.getElementById('grand-total').textContent = '₱' + total;
  }
</script>

</body>
</html>
