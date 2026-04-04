<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/profile.css">
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
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($user['first_name'] ?? 'Grower!') ?> 👋</span>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<?php
// Hardcoded — replace with DB queries later
$user = $user ?? [
  'first_name' => 'Juan',
  'last_name'  => 'Dela Cruz',
  'username'   => 'juandelacruz',
  'email'      => 'juan@email.com',
  'joined'     => 'January 2026',
];

$purchased = [
  ['emoji' => '🍅', 'name' => 'Tomato Seeds',  'qty' => 2, 'price' => 45,  'date' => 'Mar 12, 2026'],
  ['emoji' => '🥬', 'name' => 'Pechay Seeds',  'qty' => 3, 'price' => 25,  'date' => 'Mar 20, 2026'],
  ['emoji' => '🌶️', 'name' => 'Chili Seeds',   'qty' => 1, 'price' => 55,  'date' => 'Apr 1, 2026'],
];

$listings = [
  ['emoji' => '🌿', 'name' => 'Basil Seeds',      'type' => 'Herb',      'price' => 30, 'stock' => 50,  'status' => 'Active'],
  ['emoji' => '🍉', 'name' => 'Watermelon Seeds',  'type' => 'Fruit',     'price' => 60, 'stock' => 0,   'status' => 'Out of Stock'],
];
?>

<div class="sc-profile-page">

  <!-- USER INFO CARD -->
  <div class="sc-profile-card">
    <div class="sc-profile-avatar">🌱</div>
    <div class="sc-profile-info">
      <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
      <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
      <p class="sc-profile-meta">📧 <?= htmlspecialchars($user['email']) ?> &nbsp;|&nbsp; 📅 Joined <?= $user['joined'] ?></p>
    </div>
    <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
  </div>

  <div class="sc-profile-body">

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchased Seeds</h2>
        <span class="sc-section-count"><?= count($purchased) ?> orders</span>
      </div>

      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <span>🛒</span>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td><span class="sc-row-emoji"><?= $p['emoji'] ?></span> <?= htmlspecialchars($p['name']) ?></td>
                <td><?= $p['qty'] ?> sack<?= $p['qty'] > 1 ? 's' : '' ?></td>
                <td>₱<?= $p['price'] * $p['qty'] ?></td>
                <td><?= $p['date'] ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

    <!-- LISTED / SELLING SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listings</h2>
        <a href="sell-seeds.php" class="sc-section-link">+ Add Listing</a>
      </div>

      <?php if (empty($listings)): ?>
        <div class="sc-empty-state">
          <span>🌾</span>
          <p>No listings yet. <a href="sell-seeds.php">Sell your seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Type</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($listings as $l): ?>
              <tr>
                <td><span class="sc-row-emoji"><?= $l['emoji'] ?></span> <?= htmlspecialchars($l['name']) ?></td>
                <td><?= $l['type'] ?></td>
                <td>₱<?= $l['price'] ?>/sack</td>
                <td><?= $l['stock'] ?> sacks</td>
                <td><span class="sc-status <?= $l['stock'] > 0 ? 'active' : 'out' ?>"><?= $l['status'] ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
  <div class="sc-footer-links">
    <a href="index.php">Home</a>
    <a href="marketplace.php">Marketplace</a>
    <a href="planting-guide.php">Planting Guide</a>
  </div>
</footer>

</body>
</html>
