<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Marketplace</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/marketplace.css">
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

<!-- PAGE HEADER -->
<div class="sc-market-header">
  <h1>Marketplace</h1>
  <p>Browse and buy seeds from your local growers.</p>
</div>

<!-- MAIN -->
<div class="sc-market-layout">

  <!-- FILTERS SIDEBAR -->
  <aside class="sc-filters">
    <h3>Filters</h3>

    <div class="sc-filter-group">
      <label>Types</label>
      <select id="filter-type">
        <option value="">All Types</option>
        <option value="Vegetable">Vegetable</option>
        <option value="Herb">Herb</option>
        <option value="Fruit">Fruit</option>
        <option value="Flower">Flower</option>
      </select>
    </div>

    <div class="sc-filter-group">
      <label>Season</label>
      <select id="filter-season">
        <option value="">All Seasons</option>
        <option value="Dry Season">Dry Season</option>
        <option value="Wet Season">Wet Season</option>
        <option value="All Seasons">All Seasons</option>
      </select>
    </div>

    <div class="sc-filter-group">
      <label>Max Price (₱)</label>
      <input type="number" id="filter-price" placeholder="e.g. 100" min="0">
    </div>

    <button class="sc-btn-filter" onclick="applyFilters()">Apply Filters</button>
    <button class="sc-btn-reset" onclick="resetFilters()">Reset</button>
  </aside>

  <!-- SEED LISTINGS -->
  <div class="sc-market-main">

    <!-- SEARCH BAR -->
    <div class="sc-search-bar">
      <input type="text" id="search-input" placeholder="Search seeds..." oninput="applyFilters()">
      <span class="sc-search-icon">🔍</span>
    </div>

    <p class="sc-results-count" id="results-count">Showing all seeds</p>

    <!-- SEED GRID -->
    <div class="sc-market-grid" id="seed-grid">

      <div class="sc-market-card" data-type="Vegetable" data-season="Dry Season" data-price="45">
        <div class="sc-market-emoji">🍅</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Tomato Seeds</p>
          <p class="sc-market-meta">Vegetable &nbsp;|&nbsp; Dry Season</p>
          <p class="sc-market-months">📅 Apr – Jun</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱45 <span class="sc-per-sack">/ sack</span></span>
          <a href="seed-details.php?id=1" class="sc-btn-view">View</a>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Herb" data-season="Dry Season" data-price="30">
        <div class="sc-market-emoji">🌿</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Basil Seeds</p>
          <p class="sc-market-meta">Herb &nbsp;|&nbsp; Dry Season</p>
          <p class="sc-market-months">📅 Mar – May</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱30 <span class="sc-per-sack">/ sack</span></span>
          <a href="seed-details.php?id=2" class="sc-btn-view">View</a>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Vegetable" data-season="Dry Season" data-price="55">
        <div class="sc-market-emoji">🌶️</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Chili Seeds</p>
          <p class="sc-market-meta">Vegetable &nbsp;|&nbsp; Dry Season</p>
          <p class="sc-market-months">📅 Feb – Apr</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱55 <span class="sc-per-sack">/ sack</span></span>
          <a href="seed-details.php?id=3" class="sc-btn-view">View</a>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Vegetable" data-season="All Seasons" data-price="25">
        <div class="sc-market-emoji">🥬</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Pechay Seeds</p>
          <p class="sc-market-meta">Vegetable &nbsp;|&nbsp; All Seasons</p>
          <p class="sc-market-months">📅 Year-round</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱25 <span class="sc-per-sack">/ sack</span></span>
          <a href="seed-details.php?id=4" class="sc-btn-view">View</a>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Vegetable" data-season="Wet Season" data-price="40">
        <div class="sc-market-emoji">🌽</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Corn Seeds</p>
          <p class="sc-market-meta">Vegetable &nbsp;|&nbsp; Wet Season</p>
          <p class="sc-market-months">📅 May – Jul</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱40 <span class="sc-per-sack">/ sack</span></span>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Vegetable" data-season="Wet Season" data-price="35">
        <div class="sc-market-emoji">🥒</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Cucumber Seeds</p>
          <p class="sc-market-meta">Vegetable &nbsp;|&nbsp; Wet Season</p>
          <p class="sc-market-months">📅 May – Aug</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱35 <span class="sc-per-sack">/ sack</span></span>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Herb" data-season="All Seasons" data-price="28">
        <div class="sc-market-emoji">🧄</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Garlic Seeds</p>
          <p class="sc-market-meta">Herb &nbsp;|&nbsp; All Seasons</p>
          <p class="sc-market-months">📅 Year-round</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱28 <span class="sc-per-sack">/ sack</span></span>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

      <div class="sc-market-card" data-type="Fruit" data-season="Dry Season" data-price="60">
        <div class="sc-market-emoji">🍉</div>
        <div class="sc-market-info">
          <p class="sc-market-name">Watermelon Seeds</p>
          <p class="sc-market-meta">Fruit &nbsp;|&nbsp; Dry Season</p>
          <p class="sc-market-months">📅 Mar – May</p>
        </div>
        <div class="sc-market-footer">
          <span class="sc-market-price">₱60 <span class="sc-per-sack">/ sack</span></span>
          <button class="sc-btn-add">Add to Cart</button>
        </div>
      </div>

    </div>

    <!-- EMPTY STATE -->
    <div class="sc-market-empty" id="empty-state" style="display:none;">
      <span>🌱</span>
      <p>No seeds match your search.</p>
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
  function applyFilters() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const type   = document.getElementById('filter-type').value;
    const season = document.getElementById('filter-season').value;
    const price  = parseFloat(document.getElementById('filter-price').value) || Infinity;

    const cards = document.querySelectorAll('.sc-market-card');
    let visible = 0;

    cards.forEach(card => {
      const name       = card.querySelector('.sc-market-name').textContent.toLowerCase();
      const cardType   = card.dataset.type;
      const cardSeason = card.dataset.season;
      const cardPrice  = parseFloat(card.dataset.price);

      const matches =
        name.includes(search) &&
        (type   === '' || cardType   === type) &&
        (season === '' || cardSeason === season) &&
        cardPrice <= price;

      card.style.display = matches ? '' : 'none';
      if (matches) visible++;
    });

    document.getElementById('results-count').textContent =
      visible === 0 ? '' : `Showing ${visible} seed${visible !== 1 ? 's' : ''}`;
    document.getElementById('empty-state').style.display = visible === 0 ? 'flex' : 'none';
  }

  function resetFilters() {
    document.getElementById('search-input').value  = '';
    document.getElementById('filter-type').value   = '';
    document.getElementById('filter-season').value = '';
    document.getElementById('filter-price').value  = '';
    applyFilters();
  }
</script>

</body>
</html>
