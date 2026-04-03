<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Seed Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/seed-details.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php" class="active">Marketplace</a></li>
    <li><a href="planting-guide.php">Planting Guide</a></li>
  </ul>
  <a href="login.php"><button class="sc-btn-nav">Login</button></a>
</nav>

<?php

$seeds = [
   
  1 => [
    'name'        => 'Tomato Seeds',
    'emoji'       => '🍅',
    'type'        => 'Vegetable',
    'season'      => 'Dry Season',
    'months'      => 'April – June',
    'price'       => 45,
    'stock'       => 120,
    'description' => 'A popular and versatile vegetable seed perfect for home gardens and small farms. Produces firm, juicy tomatoes ideal for cooking and fresh eating.',
    'schedule'    => [
      ['step' => 'Sow Seeds',      'when' => 'April',     'note' => 'Start seeds in seedling trays indoors.'],
      ['step' => 'Transplant',     'when' => 'May',       'note' => 'Move seedlings to garden beds when 4–6 inches tall.'],
      ['step' => 'First Harvest',  'when' => 'June–July', 'note' => 'Fruits are ready when fully colored and slightly soft.'],
    ],
    'tips' => ['Full sun (6–8 hrs/day)', 'Water consistently', 'Stake or cage plants as they grow'],
  ],
  2 => [
    'name'        => 'Basil Seeds',
    'emoji'       => '🌿',
    'type'        => 'Herb',
    'season'      => 'Dry Season',
    'months'      => 'March – May',
    'price'       => 30,
    'stock'       => 200,
    'description' => 'Aromatic herb widely used in cooking. Easy to grow indoors or outdoors. Great companion plant for tomatoes.',
    'schedule'    => [
      ['step' => 'Sow Seeds',     'when' => 'March',    'note' => 'Direct sow or start in small pots.'],
      ['step' => 'Thin Seedlings','when' => 'April',    'note' => 'Keep the strongest seedlings, 6 inches apart.'],
      ['step' => 'Harvest',       'when' => 'May+',     'note' => 'Pinch leaves regularly to encourage bushy growth.'],
    ],
    'tips' => ['Partial to full sun', 'Keep soil moist but not soggy', 'Pinch flowers to extend harvest'],
  ],
  3 => [
    'name'        => 'Chili Seeds',
    'emoji'       => '🌶️',
    'type'        => 'Vegetable',
    'season'      => 'Dry Season',
    'months'      => 'February – April',
    'price'       => 55,
    'stock'       => 85,
    'description' => 'Hot and flavorful chili seeds suited for warm climates. Great for home gardens and local markets.',
    'schedule'    => [
      ['step' => 'Sow Seeds',     'when' => 'February', 'note' => 'Start in seedling trays with warm soil.'],
      ['step' => 'Transplant',    'when' => 'March',    'note' => 'Move to garden beds after 3–4 weeks.'],
      ['step' => 'First Harvest', 'when' => 'April–May','note' => 'Pick when peppers reach desired color and size.'],
    ],
    'tips' => ['Full sun required', 'Well-drained soil', 'Avoid overwatering'],
  ],
  4 => [
    'name'        => 'Pechay Seeds',
    'emoji'       => '🥬',
    'type'        => 'Vegetable',
    'season'      => 'All Seasons',
    'months'      => 'Year-round',
    'price'       => 25,
    'stock'       => 300,
    'description' => 'One of the most popular leafy vegetables in the Philippines. Fast-growing and easy to maintain, ideal for beginners.',
    'schedule'    => [
      ['step' => 'Sow Seeds',     'when' => 'Anytime',   'note' => 'Direct sow in prepared garden beds.'],
      ['step' => 'Thin Seedlings','when' => '1–2 weeks', 'note' => 'Thin to 4–6 inches apart.'],
      ['step' => 'Harvest',       'when' => '30–45 days','note' => 'Cut outer leaves or harvest whole plant.'],
    ],
    'tips' => ['Partial to full sun', 'Keep soil consistently moist', 'Fertilize every 2 weeks'],
  ],
];

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$seed = $seeds[$id] ?? $seeds[1];
?>

<div class="sc-detail-page">
<p><b>Hardcoded seed data — replace with DB query later using $_GET['id']</b></p>
  <!-- BREADCRUMB -->
  <div class="sc-breadcrumb">
    <a href="marketplace.php">Marketplace</a> <span>/</span> <?= htmlspecialchars($seed['name']) ?>
  </div>

  <!-- DETAIL CARD -->
  <div class="sc-detail-card">

    <!-- LEFT: SEED VISUAL + QUICK INFO -->
    <div class="sc-detail-left">
      <div class="sc-detail-emoji"><?= $seed['emoji'] ?></div>
      <div class="sc-detail-tags">
        <span class="sc-tag"><?= $seed['type'] ?></span>
        <span class="sc-tag"><?= $seed['season'] ?></span>
      </div>
      <div class="sc-detail-meta-list">
        <div class="sc-meta-item">
          <span class="sc-meta-label">Best Months</span>
          <span class="sc-meta-value">📅 <?= $seed['months'] ?></span>
        </div>
        <div class="sc-meta-item">
          <span class="sc-meta-label">Stock</span>
          <span class="sc-meta-value"><?= $seed['stock'] ?> packs available</span>
        </div>
      </div>
    </div>

    <!-- RIGHT: DETAILS -->
    <div class="sc-detail-right">

      <h1><?= htmlspecialchars($seed['name']) ?></h1>
      <p class="sc-detail-desc"><?= htmlspecialchars($seed['description']) ?></p>

      <!-- PLANTING SCHEDULE -->
      <div class="sc-schedule-section">
        <h2>Planting Schedule</h2>
        <div class="sc-schedule-steps">
          <?php foreach ($seed['schedule'] as $i => $step): ?>
          <div class="sc-step">
            <div class="sc-step-num"><?= $i + 1 ?></div>
            <div class="sc-step-body">
              <p class="sc-step-title"><?= htmlspecialchars($step['step']) ?> <span class="sc-step-when"><?= htmlspecialchars($step['when']) ?></span></p>
              <p class="sc-step-note"><?= htmlspecialchars($step['note']) ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- TIPS -->
      <div class="sc-tips-section">
        <h2>Growing Tips</h2>
        <ul class="sc-tips-list">
          <?php foreach ($seed['tips'] as $tip): ?>
            <li>✅ <?= htmlspecialchars($tip) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- BUY -->
      <div class="sc-buy-section">
        <span class="sc-detail-price">₱<?= $seed['price'] ?></span>
        <div class="sc-qty-wrap">
          <button class="sc-qty-btn" onclick="changeQty(-1)">−</button>
          <input type="number" id="qty" value="1" min="1" max="<?= $seed['stock'] ?>">
          <button class="sc-qty-btn" onclick="changeQty(1)">+</button>
        </div>
        <button class="sc-btn-cart">🛒 Add to Cart</button>
        <button class="sc-btn-buy">Buy Now</button>
      </div>

    </div>
  </div>
</div>

<!-- FOOTER -->
<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
  <div class="sc-footer-links">
    <a href="index.php">Home</a>
    <a href="marketplace.php">Marketplace</a>
    <a href="planting-guide.php">Planting Guide</a>
  </div>
</footer>

<script>
  function changeQty(delta) {
    const input = document.getElementById('qty');
    const max   = parseInt(input.max);
    let val     = parseInt(input.value) + delta;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
  }
</script>

</body>
</html>
