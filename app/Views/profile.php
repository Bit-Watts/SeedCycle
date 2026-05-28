<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Profile</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/profile.css">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'settings'; require __DIR__ . '/includes/sidebar.php'; ?>

  <main class="sc-main">

    <!-- USER INFO CARD -->
    <div class="sc-profile-card">
      <div class="sc-profile-avatar">
        <?php if (!empty($user['profile_image'])): ?>
          <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile">
        <?php else: ?>
          <i class="fa-solid fa-seedling"></i>
        <?php endif; ?>
      </div>
      <div class="sc-profile-info">
        <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
        <p class="sc-profile-username">@<?= htmlspecialchars($user['username']) ?></p>
        <p class="sc-profile-meta">
          <i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($user['email']) ?>
          <span class="sc-meta-sep">|</span>
          <i class="fa-solid fa-calendar"></i> Joined <?= $user['joined'] ?>
        </p>
      </div>
      <a href="settings.php" class="sc-btn-edit">Edit Profile</a>
    </div>

    <!-- MY SEED LISTINGS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2><i class="fa-solid fa-seedling"></i> My Seed Listings</h2>
        <div class="sc-section-header-actions">
          <span class="sc-section-count"><?= count($myListings) ?> listing<?= count($myListings) !== 1 ? 's' : '' ?></span>
          <a href="sell-seeds.php" class="sc-section-link"><i class="fa-solid fa-plus"></i> Add New</a>
        </div>
      </div>

      <?php if (empty($myListings)): ?>
        <div class="sc-empty-state">
          <i class="fa-solid fa-store"></i>
          <p>You haven't listed any seeds yet.</p>
          <a href="sell-seeds.php" class="sc-btn-sell-cta">Start Selling</a>
        </div>
      <?php else: ?>
        <div class="mlc-list">
          <?php foreach ($myListings as $listing): ?>
          <?php
            $statusMap = [
              'approved' => ['label' => 'Active',   'cls' => 'mlc-s-active'],
              'pending'  => ['label' => 'Pending',  'cls' => 'mlc-s-pending'],
              'rejected' => ['label' => 'Rejected', 'cls' => 'mlc-s-rejected'],
            ];
            $s = $statusMap[$listing['listing_status']] ?? ['label' => ucfirst($listing['listing_status']), 'cls' => 'mlc-s-pending'];
            $isActive   = $listing['listing_status'] === 'approved';
            $isNoStock  = (int)$listing['stock_quantity'] === 0;
          ?>
          <div class="mlc-card">

            <!-- LEFT: fixed-size image -->
            <div class="mlc-thumb">
              <?php if (!empty($listing['image_url'])): ?>
                <img src="<?= htmlspecialchars($listing['image_url']) ?>"
                     alt="<?= htmlspecialchars($listing['name']) ?>"
                     width="120" height="120" loading="lazy">
              <?php else: ?>
                <span class="mlc-thumb-icon"><i class="fa-solid fa-seedling"></i></span>
              <?php endif; ?>
            </div>

            <!-- RIGHT: all content -->
            <div class="mlc-content">

              <!-- Top row: name + status badge -->
              <div class="mlc-top">
                <h3 class="mlc-name"><?= htmlspecialchars($listing['name']) ?></h3>
                <span class="mlc-status <?= $s['cls'] ?>"><?= $s['label'] ?></span>
              </div>

              <!-- Category pill -->
              <?php if (!empty($listing['category'])): ?>
                <span class="mlc-cat"><?= htmlspecialchars($listing['category']) ?></span>
              <?php endif; ?>

              <!-- Price + stock row -->
              <div class="mlc-info-row">
                <span class="mlc-price">₱<?= number_format($listing['price'], 2) ?></span>
                <span class="mlc-divider">•</span>
                <span class="mlc-stock <?= $isNoStock ? 'mlc-stock-empty' : '' ?>">
                  <i class="fa-solid fa-layer-group"></i>
                  <?= $isNoStock ? 'Out of stock' : (int)$listing['stock_quantity'] . ' in stock' ?>
                </span>
              </div>

              <!-- Action buttons -->
              <div class="mlc-actions">
                <?php if ($isActive): ?>
                  <a href="seed-details.php?id=<?= (int)$listing['id'] ?>" class="mlc-btn mlc-btn-primary">
                    <i class="fa-solid fa-eye"></i> View Details
                  </a>
                <?php endif; ?>
                <a href="sell-seeds.php?edit=<?= (int)$listing['id'] ?>" class="mlc-btn mlc-btn-secondary">
                  <i class="fa-solid fa-pen-to-square"></i> Edit Listing
                </a>
              </div>

            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- PURCHASED SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2><i class="fa-solid fa-cart-shopping"></i> Purchase History</h2>
        <span class="sc-section-count"><?= count($purchased) ?> order<?= count($purchased) !== 1 ? 's' : '' ?></span>
      </div>
      <?php if (empty($purchased)): ?>
        <div class="sc-empty-state">
          <i class="fa-solid fa-cart-shopping"></i>
          <p>No purchases yet. <a href="marketplace.php">Browse seeds</a></p>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table">
            <thead>
              <tr>
                <th>Seed</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($purchased as $p): ?>
              <tr>
                <td><i class="fa-solid fa-seedling sc-table-icon"></i><?= htmlspecialchars($p['seed_names'] ?? '—') ?></td>
                <td>₱<?= number_format($p['total_amount'], 2) ?></td>
                <td>
                  <?php
                    $orderBadge = match(strtolower($p['status'])) {
                      'delivered' => 'sc-badge-active',
                      'cancelled' => 'sc-badge-rejected',
                      default     => 'sc-badge-pending',
                    };
                  ?>
                  <span class="sc-badge <?= $orderBadge ?>"><?= htmlspecialchars(ucfirst($p['status'])) ?></span>
                </td>
                <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

</body>
</html>
