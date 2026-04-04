<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Planting Guide</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/planting-guide.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php">Marketplace</a></li>
    <li><a href="planting-guide.php" class="active">Planting Guide</a></li>
  </ul>
  <a href="login.php"><button class="sc-btn-nav">Login</button></a>
</nav>

<!-- PAGE HEADER -->
<div class="sc-guide-header">
  <div>
    <h1>Planting Guide</h1>
    <p>Best planting times for every seed, every season.</p>
  </div>
  <div class="sc-view-toggle">
    <button class="sc-toggle-btn active" id="btn-calendar" onclick="switchView('calendar')">📅 Calendar</button>
    <button class="sc-toggle-btn" id="btn-list" onclick="switchView('list')">📋 List</button>
  </div>
</div>

<?php
// Hardcoded — replace with DB query later
$schedule = [
  ['month' => 'January',   'num' => '01', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🧅', 'name' => 'Onion',      'type' => 'Vegetable'],
    ['emoji' => '🥕', 'name' => 'Carrot',     'type' => 'Vegetable'],
    ['emoji' => '🧄', 'name' => 'Garlic',     'type' => 'Herb'],
  ]],
  ['month' => 'February',  'num' => '02', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🌶️', 'name' => 'Chili',      'type' => 'Vegetable'],
    ['emoji' => '🍆', 'name' => 'Eggplant',   'type' => 'Vegetable'],
    ['emoji' => '🧅', 'name' => 'Onion',      'type' => 'Vegetable'],
  ]],
  ['month' => 'March',     'num' => '03', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🌿', 'name' => 'Basil',      'type' => 'Herb'],
    ['emoji' => '🍉', 'name' => 'Watermelon', 'type' => 'Fruit'],
    ['emoji' => '🌶️', 'name' => 'Chili',      'type' => 'Vegetable'],
  ]],
  ['month' => 'April',     'num' => '04', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🍅', 'name' => 'Tomato',     'type' => 'Vegetable'],
    ['emoji' => '🌿', 'name' => 'Basil',      'type' => 'Herb'],
    ['emoji' => '🧅', 'name' => 'Onion',      'type' => 'Vegetable'],
  ]],
  ['month' => 'May',       'num' => '05', 'season' => 'Wet',  'crops' => [
    ['emoji' => '🌽', 'name' => 'Corn',       'type' => 'Vegetable'],
    ['emoji' => '🥒', 'name' => 'Cucumber',   'type' => 'Vegetable'],
    ['emoji' => '🎃', 'name' => 'Squash',     'type' => 'Vegetable'],
  ]],
  ['month' => 'June',      'num' => '06', 'season' => 'Wet',  'crops' => [
    ['emoji' => '🫘', 'name' => 'Beans',      'type' => 'Vegetable'],
    ['emoji' => '🥬', 'name' => 'Pechay',     'type' => 'Vegetable'],
    ['emoji' => '🌿', 'name' => 'Basil',      'type' => 'Herb'],
  ]],
  ['month' => 'July',      'num' => '07', 'season' => 'Wet',  'crops' => [
    ['emoji' => '🥬', 'name' => 'Pechay',     'type' => 'Vegetable'],
    ['emoji' => '🌽', 'name' => 'Corn',       'type' => 'Vegetable'],
    ['emoji' => '🫘', 'name' => 'Beans',      'type' => 'Vegetable'],
  ]],
  ['month' => 'August',    'num' => '08', 'season' => 'Wet',  'crops' => [
    ['emoji' => '🥬', 'name' => 'Pechay',     'type' => 'Vegetable'],
    ['emoji' => '🥕', 'name' => 'Carrot',     'type' => 'Vegetable'],
    ['emoji' => '🍆', 'name' => 'Eggplant',   'type' => 'Vegetable'],
  ]],
  ['month' => 'September', 'num' => '09', 'season' => 'Wet',  'crops' => [
    ['emoji' => '🍅', 'name' => 'Tomato',     'type' => 'Vegetable'],
    ['emoji' => '🌶️', 'name' => 'Chili',      'type' => 'Vegetable'],
    ['emoji' => '🥬', 'name' => 'Pechay',     'type' => 'Vegetable'],
  ]],
  ['month' => 'October',   'num' => '10', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🧄', 'name' => 'Garlic',     'type' => 'Herb'],
    ['emoji' => '🧅', 'name' => 'Onion',      'type' => 'Vegetable'],
    ['emoji' => '🥕', 'name' => 'Carrot',     'type' => 'Vegetable'],
  ]],
  ['month' => 'November',  'num' => '11', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🍉', 'name' => 'Watermelon', 'type' => 'Fruit'],
    ['emoji' => '🧄', 'name' => 'Garlic',     'type' => 'Herb'],
    ['emoji' => '🥕', 'name' => 'Carrot',     'type' => 'Vegetable'],
  ]],
  ['month' => 'December',  'num' => '12', 'season' => 'Dry',  'crops' => [
    ['emoji' => '🧅', 'name' => 'Onion',      'type' => 'Vegetable'],
    ['emoji' => '🧄', 'name' => 'Garlic',     'type' => 'Herb'],
    ['emoji' => '🌶️', 'name' => 'Chili',      'type' => 'Vegetable'],
  ]],
];

$currentMonth = (int) date('n');
?>

<!-- CALENDAR VIEW -->
<div id="view-calendar" class="sc-guide-content">
  <div class="sc-calendar-grid">
    <?php foreach ($schedule as $i => $m): ?>
    <div class="sc-cal-card <?= ($i + 1) === $currentMonth ? 'current' : '' ?>">
      <div class="sc-cal-header">
        <span class="sc-cal-month"><?= $m['month'] ?></span>
        <span class="sc-cal-season <?= strtolower($m['season']) ?>"><?= $m['season'] === 'Dry' ? '☀️ Dry' : '🌧️ Wet' ?></span>
      </div>
      <ul class="sc-cal-crops">
        <?php foreach ($m['crops'] as $crop): ?>
          <li><?= $crop['emoji'] ?> <?= $crop['name'] ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- LIST VIEW -->
<div id="view-list" class="sc-guide-content" style="display:none;">
  <div class="sc-list-wrap">
    <?php foreach ($schedule as $i => $m): ?>
    <div class="sc-list-row <?= ($i + 1) === $currentMonth ? 'current' : '' ?>">
      <div class="sc-list-month">
        <span class="sc-list-num"><?= $m['num'] ?></span>
        <span class="sc-list-name"><?= $m['month'] ?></span>
        <span class="sc-list-season <?= strtolower($m['season']) ?>"><?= $m['season'] === 'Dry' ? '☀️ Dry' : '🌧️ Wet' ?></span>
      </div>
      <div class="sc-list-crops">
        <?php foreach ($m['crops'] as $crop): ?>
          <span class="sc-crop-tag"><?= $crop['emoji'] ?> <?= $crop['name'] ?></span>
        <?php endforeach; ?>
      </div>
      <?php if (($i + 1) === $currentMonth): ?>
        <span class="sc-now-badge">Now</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
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

<script>
  function switchView(view) {
    document.getElementById('view-calendar').style.display = view === 'calendar' ? 'block' : 'none';
    document.getElementById('view-list').style.display     = view === 'list'     ? 'block' : 'none';
    document.getElementById('btn-calendar').classList.toggle('active', view === 'calendar');
    document.getElementById('btn-list').classList.toggle('active', view === 'list');
  }
</script>

</body>
</html>
