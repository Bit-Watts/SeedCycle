<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Marketplace</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/marketplace.css">
</head>
<body>
<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'marketplace'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">

    <div class="sc-market-header">
      <h1>Marketplace</h1>
      <p>Browse and buy seeds from your local growers.</p>
    </div>

    <?php if (!empty($ownSeedError)): ?>
      <div class="sc-alert sc-alert-warning" style="margin-bottom:16px;">
        You cannot add your own seed to the cart.
      </div>
    <?php endif; ?>

    <!-- SEARCH BAR + FILTER BUTTON -->
    <div class="sc-search-row">
      <div class="sc-search-bar">
        <input type="text" id="search-input" placeholder="Search seeds..." oninput="applyFilters()">
        <span class="sc-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
      </div>
      <button class="sc-filter-btn" id="filterToggleBtn" onclick="toggleFilterPopup()">
        <i class="fa-solid fa-sliders"></i> Filters
      </button>
    </div>

    <p class="sc-results-count" id="results-count">Showing all seeds</p>

    <!-- SEED GRID -->
    <div class="sc-market-grid" id="seed-grid">

      <?php if (!empty($seeds)): ?>
        <?php foreach ($seeds as $seed): ?>
        <?php $isOwn = in_array((int)$seed['id'], $ownedSeedIds ?? []); ?>
        <div class="sc-market-card"
             data-type="<?= htmlspecialchars($seed['category'] ?? '') ?>"
             data-price="<?= htmlspecialchars($seed['price']) ?>">
          <div class="sc-market-thumb">
            <?php if (!empty($seed['image_url'])): ?>
              <img src="<?= htmlspecialchars($seed['image_url']) ?>" alt="<?= htmlspecialchars($seed['name']) ?>">
            <?php else: ?>
              <div class="sc-market-thumb-placeholder">
                <i class="fa-solid fa-seedling"></i>
              </div>
            <?php endif; ?>
          </div>
          <div class="sc-market-info">
            <p class="sc-market-name"><?= htmlspecialchars($seed['name']) ?></p>
            <p class="sc-market-meta"><?= htmlspecialchars($seed['category'] ?? 'Seed') ?></p>
            <?php if (!empty($seed['seller_id'])): ?>
              <a href="seller-profile.php?id=<?= $seed['seller_id'] ?>" class="sc-market-seller">
                <i class="fa-solid fa-store"></i> <?= htmlspecialchars($seed['seller_first_name'] . ' ' . $seed['seller_last_name']) ?>
              </a>
            <?php endif; ?>
            <?php if (!empty($seed['avg_rating'])): ?>
              <p class="sc-market-months sc-market-rating">
                <?= str_repeat('★', (int)round($seed['avg_rating'])) ?><?= str_repeat('☆', 5 - (int)round($seed['avg_rating'])) ?>
                <span class="sc-market-rating-count">(<?= $seed['review_count'] ?>)</span>
              </p>
            <?php endif; ?>
            <?php if (!empty($seed['month_range'])): ?>
              <p class="sc-market-months"><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars($seed['month_range']) ?></p>
            <?php endif; ?>
            <?php if (!empty($seed['growing_days'])): ?>
              <p class="sc-market-months"><i class="fa-solid fa-seedling"></i> <?= (int)$seed['growing_days'] ?> days to grow</p>
            <?php endif; ?>
          </div>
          <div class="sc-market-footer">
            <span class="sc-market-price">₱<?= number_format($seed['price'], 2) ?> <span class="sc-per-sack">/ pack</span></span>
            <a href="seed-details.php?id=<?= $seed['id'] ?>" class="sc-btn-view">View</a>
            <?php if ($isOwn): ?>
              <span class="sc-own-badge">Your Seed</span>
            <?php else: ?>
              <form method="POST" action="cart-add.php">
                <input type="hidden" name="seed_id" value="<?= $seed['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="sc-btn-add">Add to Cart</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>

    <!-- EMPTY STATE -->
    <div class="sc-market-empty" id="empty-state" style="display:none;">
      <span>🌱</span>
      <p>No seeds match your search.</p>
    </div>

  </main>
</div>

<!-- FILTER POPUP -->
<div class="sc-filter-overlay" id="filterOverlay" onclick="closeFilterOnOverlay(event)">
  <div class="sc-filter-popup" id="filterPopup">
    <h3>🎛️ Filters</h3>
    <div class="sc-filter-group">
      <label>Category</label>
      <select id="filter-type">
        <option value="">All Categories</option>
        <option value="Vegetable">Vegetable</option>
        <option value="Herb">Herb</option>
        <option value="Fruit">Fruit</option>
        <option value="Flower">Flower</option>
      </select>
    </div>
    <div class="sc-filter-group">
      <label>Max Price (₱)</label>
      <input type="number" id="filter-price" placeholder="e.g. 100" min="0">
    </div>
    <div class="sc-filter-popup-actions">
      <button class="sc-btn-filter-apply" onclick="applyFilters(); closeFilterPopup()">Apply</button>
      <button class="sc-btn-filter-reset" onclick="resetFilters()">Reset</button>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

<script>
  function toggleFilterPopup() {
    const overlay = document.getElementById('filterOverlay');
    const btn     = document.getElementById('filterToggleBtn');
    const isOpen  = overlay.classList.contains('active');
    overlay.classList.toggle('active', !isOpen);
    btn.classList.toggle('active', !isOpen);
  }
  function closeFilterPopup() {
    document.getElementById('filterOverlay').classList.remove('active');
    document.getElementById('filterToggleBtn').classList.remove('active');
  }
  function closeFilterOnOverlay(e) {
    if (e.target === document.getElementById('filterOverlay')) closeFilterPopup();
  }
  function applyFilters() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const type   = document.getElementById('filter-type').value;
    const price  = parseFloat(document.getElementById('filter-price').value) || Infinity;
    const cards  = document.querySelectorAll('.sc-market-card');
    let visible  = 0;
    cards.forEach(card => {
      const name      = card.querySelector('.sc-market-name').textContent.toLowerCase();
      const cardType  = card.dataset.type;
      const cardPrice = parseFloat(card.dataset.price);
      const matches   = name.includes(search) && (type === '' || cardType === type) && cardPrice <= price;
      card.style.display = matches ? '' : 'none';
      if (matches) visible++;
    });
    document.getElementById('results-count').textContent =
      visible === 0 ? '' : `Showing ${visible} seed${visible !== 1 ? 's' : ''}`;
    document.getElementById('empty-state').style.display = visible === 0 ? 'flex' : 'none';
    const hasFilter = type !== '' || document.getElementById('filter-price').value !== '';
    document.getElementById('filterToggleBtn').classList.toggle('active', hasFilter);
  }
  function resetFilters() {
    document.getElementById('filter-type').value  = '';
    document.getElementById('filter-price').value = '';
    applyFilters();
  }
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
