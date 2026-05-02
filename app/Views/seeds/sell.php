<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Sell Seeds</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/sell-seeds.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?> 👋</span>
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
      <a href="sell-seeds.php" class="sc-sidebar-link active">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Sell Seeds</h1>
      <p>Fill in your seed details and submit for admin approval.</p>
    </div>

    <div class="sc-sell-card">

      <div class="sc-sell-header">
        <h2>New Listing Request</h2>
        <p>Your submission will be reviewed by an admin before appearing on the marketplace.</p>
      </div>

      <?php if (isset($error)): ?>
        <div class="sc-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if (isset($success)): ?>
        <div class="sc-success"><?= $success ?></div>
      <?php endif; ?>

      <form action="" method="POST" enctype="multipart/form-data">

        <!-- IMAGE UPLOAD -->
        <div class="sc-form-group">
          <label>Seed Image</label>
          <div class="sc-upload-area" id="upload-area" onclick="document.getElementById('image-input').click()">
            <img id="img-preview" src="" alt="" style="display:none; width:100%; max-height:140px; object-fit:cover; border-radius:8px;">
            <div id="upload-placeholder" class="sc-upload-placeholder">
              <span>📷</span>
              <p>Click to upload an image</p>
              <small>JPG, PNG, WEBP — max 2MB</small>
            </div>
          </div>
          <input type="file" id="image-input" name="image" accept="image/*" style="display:none;" onchange="previewImage(event)">
        </div>

        <!-- SEED NAME -->
        <div class="sc-form-group">
          <label>Seed Name <span class="sc-required">*</span></label>
          <input type="text" name="seed_name" placeholder="e.g. Tomato Seeds"
                 value="<?= htmlspecialchars($_POST['seed_name'] ?? '') ?>" required>
        </div>

        <!-- CATEGORY + PRICE -->
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Category</label>
            <select name="category">
              <option value="">Select category</option>
              <?php foreach (['Vegetable','Herb','Fruit','Flower','Grain','Other'] as $cat): ?>
                <option value="<?= $cat ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="sc-form-group">
            <label>Price per Pack (₱) <span class="sc-required">*</span></label>
            <input type="number" name="price" step="0.01" min="0.01" placeholder="e.g. 45.00"
                   value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
          </div>
        </div>

        <!-- STOCK -->
        <div class="sc-form-group sc-form-half">
          <label>Stock Quantity (packs) <span class="sc-required">*</span></label>
          <input type="number" name="stock_quantity" min="1" placeholder="e.g. 50"
                 value="<?= htmlspecialchars($_POST['stock_quantity'] ?? '') ?>" required>
        </div>

        <!-- PLANTING INFO -->
        <?php
        $mNames = ['','January','February','March','April','May','June',
                   'July','August','September','October','November','December'];
        ?>
        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Planting Start Month</label>
            <select name="planting_start_month">
              <option value="">None</option>
              <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= (int)($_POST['planting_start_month'] ?? 0) === $m ? 'selected' : '' ?>><?= $mNames[$m] ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="sc-form-group">
            <label>Planting End Month</label>
            <select name="planting_end_month">
              <option value="">None</option>
              <?php for ($m = 1; $m <= 12; $m++): ?>
                <option value="<?= $m ?>" <?= (int)($_POST['planting_end_month'] ?? 0) === $m ? 'selected' : '' ?>><?= $mNames[$m] ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="sc-form-group sc-form-half">
          <label>Growing Days</label>
          <input type="number" name="growing_days" min="1" placeholder="e.g. 60"
                 value="<?= htmlspecialchars($_POST['growing_days'] ?? '') ?>">
        </div>

        <!-- DESCRIPTION -->
        <div class="sc-form-group">
          <label>Description</label>
          <textarea name="description" rows="3" placeholder="Describe your seed — variety, quality, origin, etc."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="sc-btn-submit">Submit for Approval</button>

      </form>
    </div>

    <!-- MY PREVIOUS LISTINGS -->
    <?php if (!empty($myListings)): ?>
    <div class="sc-section sc-listings-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <span class="sc-count-badge"><?= count($myListings) ?> total</span>
      </div>
      <table class="sc-listings-table">
        <thead>
          <tr>
            <th>Seed</th>
            <th>Category</th>
            <th>Price</th>
            <th>Status</th>
            <th>Submitted</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($myListings as $ml): ?>
          <tr>
            <td class="sc-td-name">🌱 <?= htmlspecialchars($ml['seed_name'] ?? '—') ?></td>
            <td class="sc-td-muted"><?= htmlspecialchars($ml['category'] ?? '—') ?></td>
            <td class="sc-td-price">₱<?= number_format($ml['price'] ?? 0, 2) ?></td>
            <td>
              <span class="sc-status-badge sc-status-<?= htmlspecialchars($ml['status'] ?? 'pending') ?>">
                <?= ucfirst($ml['status'] ?? 'pending') ?>
              </span>
            </td>
            <td class="sc-td-muted"><?= date('M j, Y', strtotime($ml['created_at'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

  </main>
</div>

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
  function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview     = document.getElementById('img-preview');
    const placeholder = document.getElementById('upload-placeholder');
    preview.src       = URL.createObjectURL(file);
    preview.style.display       = 'block';
    placeholder.style.display   = 'none';
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
