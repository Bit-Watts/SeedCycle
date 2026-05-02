<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Orders</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/orders.css">
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
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link active">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Orders</h1>
      <p>Seeds you've purchased from the marketplace.</p>
    </div>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Purchase History</h2>
        <span style="font-size:12px; color:#888;"><?= count($orders) ?> order<?= count($orders) !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($orders)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">📦</div>
          <p>No orders yet.</p>
          <a href="marketplace.php" style="color:#2E7D32; font-weight:500;">Browse the marketplace →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seeds</th>
                <th style="padding:10px 12px; color:#2E7D32;">Total</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Date</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
              <?php
                $isCancelled = $o['status'] === 'cancelled';
                $statusColors = [
                  'pending'          => ['#fff8e1','#f57f17'],
                  'processing'       => ['#e3f2fd','#1565c0'],
                  'confirmed'        => ['#e3f2fd','#1565c0'],
                  'shipped'          => ['#e8f5e9','#2E7D32'],
                  'in_transit'       => ['#f3e5f5','#6a1b9a'],
                  'out_for_delivery' => ['#fff3e0','#e65100'],
                  'delivered'        => ['#e8f5e9','#1b5e20'],
                  'cancelled'        => ['#ffebee','#c62828'],
                ];
                $sc = $statusColors[$o['status']] ?? ['#f5f5f5','#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0; <?= $isCancelled ? 'opacity:0.7;' : '' ?>">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($o['seed_names']) ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($o['total_amount'], 2) ?></td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $o['status']))) ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                <td style="padding:10px 12px; display:flex; gap:8px; align-items:center;">
                  <?php if (!$isCancelled): ?>
                    <a href="order-tracking.php?id=<?= (int)$o['id'] ?>"
                       style="font-size:12px; color:#4CAF50; text-decoration:none; font-weight:500;">
                      Track →
                    </a>
                  <?php else: ?>
                    <form method="POST" action="orders.php" style="display:inline;"
                          onsubmit="return confirm('Remove this cancelled order from your list?')">
                      <input type="hidden" name="remove_order" value="1">
                      <input type="hidden" name="order_id" value="<?= (int)$o['id'] ?>">
                      <button type="submit"
                        style="background:#ffebee; color:#c62828; border:none; padding:5px 12px;
                               border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                        🗑 Remove
                      </button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

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
