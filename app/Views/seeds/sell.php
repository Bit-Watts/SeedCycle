<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Sell Seeds</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/sell-seeds.css">
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

<div class="sc-page">
  <div class="sc-sell-card">

    <div class="sc-sell-header">
      <h2>List Your Seeds</h2>
      <p>Fill in the details below to post your seeds on the marketplace.</p>
    </div>

    <?php if (isset($error)): ?>
      <div class="sc-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div class="sc-success"><?= $success ?></div>
    <?php endif; ?>

    <form action="" method="POST">

      <div class="sc-form-group">
        <label>Seed Image</label>
        <div class="sc-upload-area" id="upload-area" onclick="document.getElementById('image').click()">
          <img id="img-preview" src="" alt="" style="display:none;">
          <div id="upload-placeholder">
            <span>📷</span>
            <p>Click to upload an image</p>
            <small>JPG, PNG, WEBP — max 2MB</small>
          </div>
        </div>
        <input type="file" id="image" name="image" accept="image/*" style="display:none;" onchange="previewImage(event)">
      </div>

      <div class="sc-form-group">
        <label>Seed Name</label>
        <input type="text" name="name" placeholder="e.g. Tomato Seeds" required>
      </div>

      <div class="sc-form-row">
        <div class="sc-form-group">
          <label>Type</label>
          <select name="type" required>
            <option value="" disabled selected>Select type</option>
            <option value="Vegetable">Vegetable</option>
            <option value="Herb">Herb</option>
            <option value="Fruit">Fruit</option>
            <option value="Flower">Flower</option>
          </select>
        </div>
        <div class="sc-form-group">
          <label>Season</label>
          <select name="season" required>
            <option value="" disabled selected>Select season</option>
            <option value="Dry Season">Dry Season</option>
            <option value="Wet Season">Wet Season</option>
            <option value="All Seasons">All Seasons</option>
          </select>
        </div>
      </div>

      <div class="sc-form-row">
        <div class="sc-form-group">
          <label>Price per Sack (₱)</label>
          <input type="number" name="price" placeholder="e.g. 45" min="1" required>
        </div>
        <div class="sc-form-group">
          <label>Stock (sacks available)</label>
          <input type="number" name="stock" placeholder="e.g. 50" min="1" required>
        </div>
      </div>

      <div class="sc-form-group">
        <label>Best Months to Plant</label>
        <input type="text" name="months" placeholder="e.g. April – June">
      </div>

      <div class="sc-form-group">
        <label>Description</label>
        <textarea name="description" rows="4" placeholder="Describe your seeds — variety, quality, origin, etc." required></textarea>
      </div>

      <button type="submit" class="sc-btn-submit">Post Listing</button>

    </form>

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
  function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview     = document.getElementById('img-preview');
    const placeholder = document.getElementById('upload-placeholder');
    preview.src       = URL.createObjectURL(file);
    preview.style.display       = 'block';
    placeholder.style.display   = 'none';
  }
</script>

</body>
</html>
