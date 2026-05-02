<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - My Seeds</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/my-seeds.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? $user['first_name'] ?? 'Grower') ?> 👋</span>
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
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image']??"";?><?php if(!empty($pi)):?><img src="<?=htmlspecialchars($pi)?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><?php else:?>🌱<?php endif;?></div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image'] ?? ''; ?>
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
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image']??"";?><?php if(!empty($pi)):?><img src="<?=htmlspecialchars($pi)?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;"><?php else:?>🌱<?php endif;?></div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
SESSION['profile_image'] ?? '';
        ?>
        <?php if (!empty($profileImg)): ?>
          <img src="<?= htmlspecialchars($profileImg) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
        <?php else: ?>
          🌱
        <?php endif; ?>
      </div>
      <p class="sc-sidebar-name"><?= htmlspecialchars($user['first_name'] ?? 'Grower') ?></p>
      <p class="sc-sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Overview</a>
      <a href="my-seeds.php" class="sc-sidebar-link active">🌾 My Seeds</a>
      <a href="sell-seeds.php" class="sc-sidebar-link">➕ Sell Seeds</a>
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>My Seeds</h1>
      <p>Seeds you've submitted for listing on the marketplace.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:4px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2; margin-bottom:4px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>My Listing Requests</h2>
        <a href="sell-seeds.php" class="sc-section-link">➕ Submit New Seed</a>
      </div>

      <?php if (empty($seeds)): ?>
        <div style="text-align:center; padding: 40px 0; color: #888;">
          <div style="font-size:40px; margin-bottom:12px;">🌾</div>
          <p>You haven't submitted any seeds yet.</p>
          <a href="sell-seeds.php" style="color:#2E7D32; font-weight:500;">Submit a seed →</a>
        </div>
      <?php else: ?>
        <div class="sc-table-wrap">
          <table class="sc-table" style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
              <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
                <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
                <th style="padding:10px 12px; color:#2E7D32;">Category</th>
                <th style="padding:10px 12px; color:#2E7D32;">Price</th>
                <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
                <th style="padding:10px 12px; color:#2E7D32;">Status</th>
                <th style="padding:10px 12px; color:#2E7D32;">Submitted</th>
                <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($seeds as $s):
                $statusColors = [
                  'pending'  => ['#fff8e1', '#f57f17'],
                  'approved' => ['#e8f5e9', '#2E7D32'],
                  'rejected' => ['#ffebee', '#c62828'],
                ];
                $sc = $statusColors[$s['status']] ?? ['#f5f5f5', '#555'];
              ?>
              <tr style="border-bottom:1px solid #f0f0f0;">
                <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['seed_name'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#666;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
                <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'] ?? 0, 2) ?></td>
                <td style="padding:10px 12px;"><?= (int)($s['stock_quantity'] ?? 0) ?> packs</td>
                <td style="padding:10px 12px;">
                  <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600;
                    background:<?= $sc[0] ?>; color:<?= $sc[1] ?>;">
                    <?= ucfirst($s['status'] ?? 'pending') ?>
                  </span>
                </td>
                <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($s['created_at'])) ?></td>
                <td style="padding:10px 12px;">
                  <?php if ($s['status'] === 'approved'): ?>
                    <button onclick="openAddStock(<?= (int)$s['inventory_id'] ?>, '<?= htmlspecialchars($s['seed_name'], ENT_QUOTES) ?>')"
                      style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif; font-weight:500;">
                      + Add Stock
                    </button>
                  <?php else: ?>
                    <span style="font-size:12px; color:#bbb;">—</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

<!-- ADD STOCK MODAL -->
<div class="sc-logout-overlay" id="addStockOverlay" onclick="if(event.target===this) closeAddStock()">
  <div class="sc-logout-modal" style="max-width:340px;">
    <div class="sc-logout-icon">📦</div>
    <h3 id="addStockTitle">Add Stock</h3>
    <p id="addStockSeedName" style="font-size:13px; color:#2E7D32; font-weight:600; margin-bottom:12px;"></p>
    <form method="POST" action="my-seeds.php">
      <input type="hidden" name="add_stock" value="1">
      <input type="hidden" name="inventory_id" id="addStockInventoryId">
      <div style="margin-bottom:16px;">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:6px; text-align:left;">Quantity to Add</label>
        <input type="number" name="qty" min="1" placeholder="e.g. 50" required
          style="width:100%; padding:10px 12px; border:1.5px solid #c8e6c9; border-radius:8px; font-size:14px; font-family:'Roboto',sans-serif; outline:none; text-align:center;">
      </div>
      <div class="sc-logout-actions">
        <button type="submit" class="sc-logout-confirm">Add Stock</button>
        <button type="button" class="sc-logout-cancel" onclick="closeAddStock()">Cancel</button>
      </div>
    </form>
  </div>
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
  function openAddStock(inventoryId, seedName) {
    document.getElementById('addStockInventoryId').value = inventoryId;
    document.getElementById('addStockSeedName').textContent = seedName;
    document.getElementById('addStockOverlay').classList.add('active');
  }
  function closeAddStock() {
    document.getElementById('addStockOverlay').classList.remove('active');
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
