<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Marketplace</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/marketplace.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?> 👋</span>
      <a href="cart.php" class="sc-nav-icon" title="Cart">🛒</a>
      <a href="profile.php" class="sc-nav-icon" title="Profile">👤</a>
      <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
    <?php else: ?>
      <a href="login.php"><button class="sc-btn-nav">Login</button></a>
    <?php endif; ?>
  </div>
</nav>

<div class="sc-dashboard">

  <?php if (isset($_SESSION['user_id'])): ?>
  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar" style="overflow:hidden;">
        <?php $pi = $_SESSION['profile_image'] ?? ''; ?>
        <?php if (!empty($pi)): ?>
          <img src="<?= htmlspecialchars($pi) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link active">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>
  <?php endif; ?>

  <main class="sc-main">

    <div class="sc-market-header">
      <h1>Marketplace</h1>
      <p>Browse and buy seeds from your local growers.</p>
    </div>

    <?php if (!empty($ownSeedError)): ?>
      <div style="background:#fff8e1; color:#f57f17; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffe082; margin-bottom:16px;">
        You cannot add your own seed to the cart.
      </div>
    <?php endif; ?>

    <!-- SEARCH BAR + FILTER BUTTON -->
    <div style="display:flex; gap:10px; margin-bottom:12px; align-items:center;">
      <div class="sc-search-bar" style="flex:1;">
        <input type="text" id="search-input" placeholder="Search seeds..." oninput="applyFilters()">
        <span class="sc-search-icon">🔍</span>
      </div>
      <button class="sc-filter-btn" id="filterToggleBtn" onclick="toggleFilterPopup()">
        🎛️ Filters
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
          <div class="sc-market-emoji">
            <?php if (!empty($seed['image_url'])): ?>
              <img src="<?= htmlspecialchars($seed['image_url']) ?>" alt="<?= htmlspecialchars($seed['name']) ?>"
                   style="width:60px; height:60px; object-fit:cover; border-radius:10px;">
            <?php else: ?>
              🌱
            <?php endif; ?>
          </div>
          <div class="sc-market-info">
            <p class="sc-market-name"><?= htmlspecialchars($seed['name']) ?></p>
            <p class="sc-market-meta"><?= htmlspecialchars($seed['category'] ?? 'Seed') ?></p>
            <?php if (!empty($seed['avg_rating'])): ?>
              <p class="sc-market-months" style="color:#FFC107;">
                <?= str_repeat('★', (int)round($seed['avg_rating'])) ?><?= str_repeat('☆', 5 - (int)round($seed['avg_rating'])) ?>
                <span style="color:#888; font-size:11px;">(<?= $seed['review_count'] ?>)</span>
              </p>
            <?php endif; ?>
            <?php if (!empty($seed['month_range'])): ?>
              <p class="sc-market-months">📅 <?= htmlspecialchars($seed['month_range']) ?></p>
            <?php endif; ?>
            <?php if (!empty($seed['growing_days'])): ?>
              <p class="sc-market-months">🌱 <?= (int)$seed['growing_days'] ?> days to grow</p>
            <?php endif; ?>
          </div>
          <div class="sc-market-footer">
            <span class="sc-market-price">₱<?= number_format($seed['price'], 2) ?> <span class="sc-per-sack">/ pack</span></span>
            <a href="seed-details.php?id=<?= $seed['id'] ?>" class="sc-btn-view">View</a>
            <?php if ($isOwn): ?>
              <span class="sc-own-badge">Your Seed</span>
            <?php else: ?>
              <form method="POST" action="cart-add.php" style="display: inline;">
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

<!-- FOOTER -->
<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

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
