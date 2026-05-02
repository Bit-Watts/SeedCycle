<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Seeds</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span> <span style="font-size:12px; background:#FFC107; color:#333; padding:2px 8px; border-radius:4px; margin-left:8px; font-family:'Roboto',sans-serif; font-weight:600;">ADMIN</span></div>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Admin 👋</span>
    <a href="../logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-dashboard">
  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar">🛡️</div>
      <p class="sc-sidebar-name">Admin Panel</p>
    </div>
    <nav class="sc-sidebar-nav">
      <a href="index.php" class="sc-sidebar-link">📊 Dashboard</a>
      <a href="users.php" class="sc-sidebar-link">👥 Users</a>
      <a href="seeds.php" class="sc-sidebar-link active">🌱 Seeds</a>
      <a href="listings.php" class="sc-sidebar-link">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="shipments.php" class="sc-sidebar-link">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Seeds / Inventory</h1>
      <p>Manage seed inventory.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div style="background:#ffebee; color:#c62828; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #ffcdd2;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- EDIT FORM (only shown when editing an existing seed) -->
    <?php if ($editSeed): ?>
    <div class="sc-admin-form">
      <h3>✏️ Edit Seed</h3>
      <form method="POST" action="seeds.php?edit=<?= (int)$editSeed['id'] ?>">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="seed_id" value="<?= (int)$editSeed['id'] ?>">

        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Seed Name *</label>
            <input type="text" name="name" required placeholder="e.g. Tomato Seeds"
                   value="<?= htmlspecialchars($editSeed['name'] ?? '') ?>">
          </div>
          <div class="sc-form-group">
            <label>Category</label>
            <select name="category">
              <option value="">Select category</option>
              <?php foreach (['Vegetable','Herb','Fruit','Flower','Grain','Other'] as $cat): ?>
                <option value="<?= $cat ?>" <?= ($editSeed['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="sc-form-group">
          <label>Description</label>
          <textarea name="description" rows="2" placeholder="Brief description..."><?= htmlspecialchars($editSeed['description'] ?? '') ?></textarea>
        </div>

        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Price (₱) *</label>
            <input type="number" name="price" step="0.01" min="0" required placeholder="0.00"
                   value="<?= htmlspecialchars($editSeed['price'] ?? '') ?>">
          </div>
          <div class="sc-form-group">
            <label>Stock Quantity</label>
            <input type="number" name="stock_quantity" min="0" placeholder="0"
                   value="<?= htmlspecialchars($editSeed['stock_quantity'] ?? '0') ?>">
          </div>
        </div>

        <div class="sc-form-row">
          <div class="sc-form-group">
            <label>Planting Start Month</label>
            <select name="planting_start_month">
              <option value="">None</option>
              <?php for ($m = 1; $m <= 12; $m++):
                $mName = Seed::monthName($m);
              ?>
                <option value="<?= $m ?>" <?= (int)($editSeed['planting_start_month'] ?? 0) === $m ? 'selected' : '' ?>><?= $mName ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="sc-form-group">
            <label>Planting End Month</label>
            <select name="planting_end_month">
              <option value="">None</option>
              <?php for ($m = 1; $m <= 12; $m++):
                $mName = Seed::monthName($m);
              ?>
                <option value="<?= $m ?>" <?= (int)($editSeed['planting_end_month'] ?? 0) === $m ? 'selected' : '' ?>><?= $mName ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <div class="sc-form-group" style="max-width:200px;">
          <label>Growing Days</label>
          <input type="number" name="growing_days" min="0" placeholder="e.g. 60"
                 value="<?= htmlspecialchars($editSeed['growing_days'] ?? '') ?>">
        </div>

        <button type="submit" class="sc-btn-submit">Update Seed</button>
        <a href="seeds.php" class="sc-btn-cancel">Cancel</a>
      </form>
    </div>
    <?php endif; ?>

    <!-- SEEDS TABLE -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>All Seeds</h2>
        <span style="font-size:12px; color:#888;"><?= count($seeds) ?> total</span>
      </div>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">Name</th>
              <th style="padding:10px 12px; color:#2E7D32;">Category</th>
              <th style="padding:10px 12px; color:#2E7D32;">Price</th>
              <th style="padding:10px 12px; color:#2E7D32;">Stock</th>
              <th style="padding:10px 12px; color:#2E7D32;">Status</th>
              <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($seeds as $s): ?>
            <tr style="border-bottom:1px solid #f0f0f0; <?= !$s['is_active'] ? 'opacity:0.6;' : '' ?>">
              <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($s['name']) ?></td>
              <td style="padding:10px 12px; color:#555;"><?= htmlspecialchars($s['category'] ?? '—') ?></td>
              <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">₱<?= number_format($s['price'], 2) ?></td>
              <td style="padding:10px 12px;"><?= (int)$s['stock_quantity'] ?></td>
              <td style="padding:10px 12px;">
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
                  background:<?= $s['is_active'] ? '#e8f5e9' : '#ffebee' ?>;
                  color:<?= $s['is_active'] ? '#2E7D32' : '#c62828' ?>;">
                  <?= $s['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td style="padding:10px 12px; display:flex; gap:6px; align-items:center;">
                <a href="seeds.php?edit=<?= (int)$s['id'] ?>"
                   style="background:#e3f2fd; color:#1565c0; border:none; padding:5px 10px; border-radius:6px; font-size:12px; cursor:pointer; text-decoration:none; font-family:'Roboto',sans-serif;">Edit</a>
                <form method="POST" action="seeds.php" style="display:inline;">
                  <input type="hidden" name="seed_id" value="<?= (int)$s['id'] ?>">
                  <?php if ($s['is_active']): ?>
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" style="background:#ffebee; color:#c62828; border:none; padding:5px 10px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif;"
                      onclick="return confirm('Deactivate this seed?')">Deactivate</button>
                  <?php else: ?>
                    <input type="hidden" name="action" value="restore">
                    <button type="submit" style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 10px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif;">Restore</button>
                  <?php endif; ?>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle Admin.</p>
</footer>

</body>
</html>
