<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Seed Details</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/seed-details.css">
  <link rel="stylesheet" href="assets/css/reviews.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="sc-nav-user">
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?> 👋</span>
      <a href="cart.php" class="sc-nav-icon" title="Cart">🛒</a>
      <a href="profile.php" class="sc-nav-icon" title="Profile">👤</a>
      <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
    </div>
  <?php else: ?>
    <a href="login.php"><button class="sc-btn-nav">Login</button></a>
  <?php endif; ?>
</nav>

<?php
// $seed is passed from SeedController with pre-formatted 'month_range' key
// $isOwnSeed is true if the logged-in user owns this seed listing
?>

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

    <div class="sc-breadcrumb" style="font-size:13px; margin-bottom:16px;">
      <a href="marketplace.php" style="color:#4CAF50; text-decoration:none;">← Marketplace</a>
      <span style="color:#888; margin:0 6px;">/</span>
      <span style="color:#333;"><?= htmlspecialchars($seed['name']) ?></span>
    </div>

    <div class="sc-detail-card">

      <!-- LEFT -->
      <div class="sc-detail-left">
        <div class="sc-detail-emoji">
          <?php if (!empty($seed['image_url'])): ?>
            <img src="<?= htmlspecialchars($seed['image_url']) ?>" alt="<?= htmlspecialchars($seed['name']) ?>"
                 style="width:100%; max-height:200px; object-fit:cover; border-radius:12px;">
          <?php else: ?>
            🌱
          <?php endif; ?>
        </div>
        <div class="sc-detail-tags">
          <?php if (!empty($seed['category'])): ?>
            <span class="sc-tag"><?= htmlspecialchars($seed['category']) ?></span>
          <?php endif; ?>
        </div>
        <div class="sc-detail-meta-list">
          <?php if (!empty($seed['month_range'])): ?>
          <div class="sc-meta-item">
            <span class="sc-meta-label">Best Months</span>
            <span class="sc-meta-value">📅 <?= htmlspecialchars($seed['month_range']) ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($seed['growing_days'])): ?>
          <div class="sc-meta-item">
            <span class="sc-meta-label">Growing Time</span>
            <span class="sc-meta-value">🌱 <?= (int)$seed['growing_days'] ?> days</span>
          </div>
          <?php endif; ?>
          <div class="sc-meta-item">
            <span class="sc-meta-label">Stock</span>
            <span class="sc-meta-value"><?= (int)$seed['stock_quantity'] ?> packs available</span>
          </div>
        </div>
      </div>

      <!-- RIGHT -->
      <div class="sc-detail-right">
        <h1><?= htmlspecialchars($seed['name']) ?></h1>
        <?php if (!empty($seed['description'])): ?>
          <p class="sc-detail-desc"><?= nl2br(htmlspecialchars($seed['description'])) ?></p>
        <?php endif; ?>

        <div class="sc-buy-section">
          <span class="sc-detail-price">₱<?= number_format($seed['price'], 2) ?></span>
          <?php if (!empty($isOwnSeed)): ?>
            <div style="background:#fff8e1; color:#f57f17; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:600; border:1px solid #ffe082; margin-top:10px;">
              🌾 This is your own seed listing — you cannot purchase it.
            </div>
          <?php else: ?>
            <form method="POST" action="cart-add.php" style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px;">
              <input type="hidden" name="seed_id" value="<?= (int)$seed['id'] ?>">
              <div class="sc-qty-wrap">
                <button type="button" class="sc-qty-btn" onclick="changeQty(-1)">−</button>
                <input type="number" id="qty" name="quantity" value="1" min="1" max="<?= (int)$seed['stock_quantity'] ?>">
                <button type="button" class="sc-qty-btn" onclick="changeQty(1)">+</button>
              </div>
              <button type="submit" class="sc-btn-cart">🛒 Add to Cart</button>
            </form>
          <?php endif; ?>
        </div>
      </div>

    </div>

  </main>
</div>

<!-- REVIEWS SECTION -->
<?php
function renderStars(float $rating, bool $interactive = false): string {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= round($rating) ? '★' : '☆';
    }
    return $stars;
}
?>

<div class="sc-dashboard" style="margin-top:0;">
  <?php if (isset($_SESSION['user_id'])): ?>
  <aside class="sc-sidebar" style="visibility:hidden;"></aside>
  <?php endif; ?>
  <main class="sc-main" style="padding-top:0;">

    <div class="sc-reviews-section sc-section">
      <div class="sc-section-header">
        <h2>⭐ Reviews</h2>
        <span style="font-size:12px; color:#888;"><?= $ratingData['total'] ?> review<?= $ratingData['total'] !== 1 ? 's' : '' ?></span>
      </div>

      <!-- Flash messages -->
      <?php if (!empty($reviewSuccess)): ?>
        <div class="sc-review-alert success">Your review has been submitted. Thank you!</div>
      <?php endif; ?>
      <?php if (!empty($reportSuccess)): ?>
        <div class="sc-review-alert success">Report submitted. Our team will review it.</div>
      <?php endif; ?>
      <?php if (!empty($reviewError)): ?>
        <?php $msgs = ['invalid'=>'Invalid rating.','already'=>'You have already reviewed this seed.','not_purchased'=>'You can only review seeds you have purchased and received.']; ?>
        <div class="sc-review-alert error"><?= $msgs[$reviewError] ?? 'Something went wrong.' ?></div>
      <?php endif; ?>

      <!-- Rating summary -->
      <?php if ($ratingData['total'] > 0): ?>
      <div class="sc-rating-summary">
        <div class="sc-rating-big"><?= number_format($ratingData['avg'], 1) ?></div>
        <div>
          <div class="sc-rating-stars"><?= renderStars($ratingData['avg']) ?></div>
          <div class="sc-rating-count"><?= $ratingData['total'] ?> review<?= $ratingData['total'] !== 1 ? 's' : '' ?></div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Write review form (only for buyers who received the seed) -->
      <?php if (!empty($canReview)): ?>
      <div class="sc-write-review">
        <h3>Write a Review</h3>
        <form method="POST" action="review-submit.php">
          <input type="hidden" name="inventory_id" value="<?= (int)$seed['id'] ?>">
          <div class="sc-star-input">
            <?php for ($i = 5; $i >= 1; $i--): ?>
              <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
              <label for="star<?= $i ?>">★</label>
            <?php endfor; ?>
          </div>
          <textarea name="comment" class="sc-review-textarea" placeholder="Share your experience with this seed..."></textarea>
          <button type="submit" class="sc-btn-submit-review">Submit Review</button>
        </form>
      </div>
      <?php elseif (isset($_SESSION['user_id']) && !$isOwnSeed && !$hasReviewed): ?>
        <p style="font-size:13px; color:#888; margin-bottom:16px;">
          Only buyers who have received this seed can leave a review.
        </p>
      <?php endif; ?>

      <!-- Report seed button -->
      <?php if (isset($_SESSION['user_id']) && !$isOwnSeed): ?>
      <div style="margin-bottom:16px;">
        <button class="sc-btn-report-review" onclick="openReport('seed', <?= (int)$seed['id'] ?>, 'seed-details.php?id=<?= (int)$seed['id'] ?>')">
          🚩 Report this seed
        </button>
      </div>
      <?php endif; ?>

      <!-- Reviews list -->
      <?php if (empty($reviews)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No reviews yet. Be the first to review!</p>
      <?php else: ?>
        <?php foreach ($reviews as $rv): ?>
        <div class="sc-review-card">
          <div class="sc-review-header">
            <div class="sc-review-user">
              <div class="sc-review-avatar">
                <?php if (!empty($rv['profile_image'])): ?>
                  <img src="<?= htmlspecialchars($rv['profile_image']) ?>" alt="">
                <?php else: ?>
                  🌱
                <?php endif; ?>
              </div>
              <div>
                <div class="sc-review-name"><?= htmlspecialchars($rv['first_name'] . ' ' . $rv['last_name']) ?></div>
                <div class="sc-review-date"><?= date('M j, Y', strtotime($rv['created_at'])) ?></div>
              </div>
            </div>
            <div class="sc-review-rating sc-stars"><?= renderStars($rv['rating']) ?></div>
          </div>
          <?php if (!empty($rv['comment'])): ?>
            <p class="sc-review-comment"><?= htmlspecialchars($rv['comment']) ?></p>
          <?php endif; ?>
          <div class="sc-review-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
              <button class="sc-btn-report-review"
                onclick="openReport('review', <?= (int)$rv['id'] ?>, 'seed-details.php?id=<?= (int)$seed['id'] ?>')">
                🚩 Report
              </button>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>

  </main>
</div>

<!-- REPORT MODAL -->
<div class="sc-report-overlay" id="reportOverlay" onclick="if(event.target===this)closeReport()">
  <div class="sc-report-modal">
    <h3>🚩 Report</h3>
    <form method="POST" action="report-submit.php">
      <input type="hidden" name="type"     id="report_type">
      <input type="hidden" name="target_id" id="report_target">
      <input type="hidden" name="redirect"  id="report_redirect">
      <select name="reason" required>
        <option value="" disabled selected>Select a reason</option>
        <option value="spam">Spam</option>
        <option value="fake">Fake / Misleading</option>
        <option value="inappropriate">Inappropriate content</option>
        <option value="wrong_info">Wrong information</option>
        <option value="other">Other</option>
      </select>
      <textarea name="details" placeholder="Additional details (optional)" rows="3"></textarea>
      <div class="sc-report-actions">
        <button type="submit" class="sc-btn-report-submit">Submit Report</button>
        <button type="button" class="sc-btn-report-cancel" onclick="closeReport()">Cancel</button>
      </div>
    </form>
  </div>
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
  function changeQty(delta) {
    const input = document.getElementById('qty');
    const max   = parseInt(input.max);
    let val     = parseInt(input.value) + delta;
    if (val < 1)   val = 1;
    if (val > max) val = max;
    input.value = val;
  }
  function openReport(type, targetId, redirect) {
    document.getElementById('report_type').value     = type;
    document.getElementById('report_target').value   = targetId;
    document.getElementById('report_redirect').value = redirect;
    document.getElementById('reportOverlay').classList.add('active');
  }
  function closeReport() {
    document.getElementById('reportOverlay').classList.remove('active');
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
