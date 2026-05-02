<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Users</title>
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
      <a href="users.php" class="sc-sidebar-link active">👥 Users</a>
      <a href="seeds.php" class="sc-sidebar-link">🌱 Seeds</a>
      <a href="listings.php" class="sc-sidebar-link">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="shipments.php" class="sc-sidebar-link">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Users</h1>
      <p>Manage all registered users.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9;">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <div class="sc-section">
      <div class="sc-section-header">
        <h2>All Users</h2>
        <span style="font-size:12px; color:#888;"><?= count($users) ?> user<?= count($users) !== 1 ? 's' : '' ?></span>
      </div>

      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">Name</th>
              <th style="padding:10px 12px; color:#2E7D32;">Username</th>
              <th style="padding:10px 12px; color:#2E7D32;">Email</th>
              <th style="padding:10px 12px; color:#2E7D32;">Role</th>
              <th style="padding:10px 12px; color:#2E7D32;">Status</th>
              <th style="padding:10px 12px; color:#2E7D32;">Joined</th>
              <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px; font-weight:500;"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
              <td style="padding:10px 12px; color:#555;">@<?= htmlspecialchars($u['username']) ?></td>
              <td style="padding:10px 12px; color:#555;"><?= htmlspecialchars($u['email']) ?></td>
              <td style="padding:10px 12px;">
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
                  background:<?= $u['role'] === 'admin' ? '#fff8e1' : '#e8f5e9' ?>;
                  color:<?= $u['role'] === 'admin' ? '#f57f17' : '#2E7D32' ?>;">
                  <?= htmlspecialchars(ucfirst($u['role'] ?? 'user')) ?>
                </span>
              </td>
              <td style="padding:10px 12px;">
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:500;
                  background:<?= $u['is_active'] ? '#e8f5e9' : '#ffebee' ?>;
                  color:<?= $u['is_active'] ? '#2E7D32' : '#c62828' ?>;">
                  <?= $u['is_active'] ? 'Active' : 'Inactive' ?>
                </span>
              </td>
              <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
              <td style="padding:10px 12px;">
                <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                <form method="POST" action="users.php" style="display:inline;">
                  <input type="hidden" name="user_id" value="<?= (int)$u['id'] ?>">
                  <?php if ($u['is_active']): ?>
                    <input type="hidden" name="action" value="deactivate">
                    <button type="submit" style="background:#ffebee; color:#c62828; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif;"
                      onclick="return confirm('Deactivate this user?')">Deactivate</button>
                  <?php else: ?>
                    <input type="hidden" name="action" value="activate">
                    <button type="submit" style="background:#e8f5e9; color:#2E7D32; border:none; padding:5px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-family:'Roboto',sans-serif;">Activate</button>
                  <?php endif; ?>
                </form>
                <?php else: ?>
                  <span style="font-size:12px; color:#aaa;">You</span>
                <?php endif; ?>
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
