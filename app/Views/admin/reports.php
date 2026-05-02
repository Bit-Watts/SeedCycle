<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Reports</title>
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
      <a href="seeds.php" class="sc-sidebar-link">🌱 Seeds</a>
      <a href="listings.php" class="sc-sidebar-link">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="shipments.php" class="sc-sidebar-link">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link active">📈 Reports & Reviews</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Reports & Analytics</h1>
      <p>Revenue, sales, user activity, and user reports.</p>
    </div>

    <!-- REVENUE STATS -->
    <div class="sc-stats-grid">
      <div class="sc-stat-card">
        <div class="sc-stat-icon">💰</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value">₱<?= number_format($totalRevenue, 0) ?></span>
          <span class="sc-stat-label">Total Revenue</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">📅</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value">₱<?= number_format($monthRevenue, 0) ?></span>
          <span class="sc-stat-label">This Month</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">✅</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($ordersByStatus['delivered'] ?? 0) ?></span>
          <span class="sc-stat-label">Delivered Orders</span>
        </div>
      </div>
      <div class="sc-stat-card">
        <div class="sc-stat-icon">🚩</div>
        <div class="sc-stat-info">
          <span class="sc-stat-value"><?= (int)($pendingReports ?? 0) ?></span>
          <span class="sc-stat-label">Pending Reports</span>
        </div>
      </div>
    </div>

    <!-- USER REPORTS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>🚩 User Reports</h2>
        <span style="font-size:12px; color:#888;"><?= count($userReports ?? []) ?> total</span>
      </div>

      <?php if (!empty($reportMessage)): ?>
        <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; margin-bottom:12px;"><?= htmlspecialchars($reportMessage) ?></div>
      <?php endif; ?>

      <?php if (empty($userReports)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No reports yet.</p>
      <?php else: ?>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">Type</th>
              <th style="padding:10px 12px; color:#2E7D32;">Target ID</th>
              <th style="padding:10px 12px; color:#2E7D32;">Reason</th>
              <th style="padding:10px 12px; color:#2E7D32;">Details</th>
              <th style="padding:10px 12px; color:#2E7D32;">Reporter</th>
              <th style="padding:10px 12px; color:#2E7D32;">Date</th>
              <th style="padding:10px 12px; color:#2E7D32;">Status</th>
              <th style="padding:10px 12px; color:#2E7D32;">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($userReports as $r): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px;">
                <span style="padding:3px 8px; border-radius:6px; font-size:11px; font-weight:600;
                  background:<?= $r['type'] === 'seed' ? '#e8f5e9' : '#fff8e1' ?>;
                  color:<?= $r['type'] === 'seed' ? '#2E7D32' : '#f57f17' ?>;">
                  <?= $r['type'] === 'seed' ? '🌱 Seed' : '⭐ Review' ?>
                </span>
              </td>
              <td style="padding:10px 12px; color:#555;">#<?= (int)$r['target_id'] ?></td>
              <td style="padding:10px 12px; font-weight:500;"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['reason']))) ?></td>
              <td style="padding:10px 12px; color:#555; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                <?= htmlspecialchars($r['details'] ?: '—') ?>
              </td>
              <td style="padding:10px 12px;">
                <?= htmlspecialchars($r['reporter_first'] . ' ' . $r['reporter_last']) ?><br>
                <span style="font-size:11px; color:#888;"><?= htmlspecialchars($r['reporter_email']) ?></span>
              </td>
              <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($r['created_at'])) ?></td>
              <td style="padding:10px 12px;">
                <?php
                $rc = ['pending'=>['#fff8e1','#f57f17'], 'reviewed'=>['#e8f5e9','#2E7D32'], 'dismissed'=>['#f5f5f5','#888']];
                $c  = $rc[$r['status']] ?? ['#f5f5f5','#555'];
                ?>
                <span style="padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:<?= $c[0] ?>; color:<?= $c[1] ?>;">
                  <?= ucfirst($r['status']) ?>
                </span>
              </td>
              <td style="padding:10px 12px; display:flex; gap:6px; flex-wrap:wrap;">
                <?php if ($r['status'] === 'pending'): ?>
                <form method="POST" action="reports.php" style="display:inline;">
                  <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="report_action" value="reviewed">
                  <button type="submit" style="background:#e8f5e9; color:#2E7D32; border:none; padding:4px 10px; border-radius:6px; font-size:11px; cursor:pointer; font-family:'Roboto',sans-serif;">Mark Reviewed</button>
                </form>
                <form method="POST" action="reports.php" style="display:inline;">
                  <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="report_action" value="dismissed">
                  <button type="submit" style="background:#f5f5f5; color:#888; border:none; padding:4px 10px; border-radius:6px; font-size:11px; cursor:pointer; font-family:'Roboto',sans-serif;">Dismiss</button>
                </form>
                <?php if ($r['type'] === 'review'): ?>
                <form method="POST" action="reports.php" style="display:inline;" onsubmit="return confirm('Delete this review?')">
                  <input type="hidden" name="report_id" value="<?= (int)$r['id'] ?>">
                  <input type="hidden" name="review_id" value="<?= (int)$r['target_id'] ?>">
                  <input type="hidden" name="report_action" value="delete_review">
                  <button type="submit" style="background:#ffebee; color:#c62828; border:none; padding:4px 10px; border-radius:6px; font-size:11px; cursor:pointer; font-family:'Roboto',sans-serif;">Delete Review</button>
                </form>
                <?php endif; ?>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- ORDER STATUS BREAKDOWN -->
    <div class="sc-section">
      <div class="sc-section-header"><h2>📊 Orders by Status</h2></div>
      <div style="display:flex; flex-wrap:wrap; gap:12px; padding:8px 0;">
        <?php
        $statusColors = [
            'pending'          => ['#fff8e1', '#f57f17'],
            'processing'       => ['#e3f2fd', '#1565c0'],
            'confirmed'        => ['#e3f2fd', '#1565c0'],
            'shipped'          => ['#e8f5e9', '#2E7D32'],
            'out_for_delivery' => ['#f3e5f5', '#6a1b9a'],
            'delivered'        => ['#e8f5e9', '#1b5e20'],
            'cancelled'        => ['#ffebee', '#c62828'],
        ];
        foreach ($ordersByStatus as $st => $cnt):
          $colors = $statusColors[$st] ?? ['#f5f5f5', '#555'];
        ?>
        <div style="background:<?= $colors[0] ?>; color:<?= $colors[1] ?>; padding:12px 20px; border-radius:10px; text-align:center; min-width:120px;">
          <div style="font-family:'Poppins',sans-serif; font-size:22px; font-weight:700;"><?= (int)$cnt ?></div>
          <div style="font-size:12px; font-weight:500; margin-top:4px;"><?= ucwords(str_replace('_', ' ', $st)) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- TOP SELLING SEEDS -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>🌱 Top Selling Seeds</h2>
        <span style="font-size:12px; color:#888;">Top 10</span>
      </div>
      <?php if (empty($topSeeds)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No sales data yet.</p>
      <?php else: ?>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">#</th>
              <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
              <th style="padding:10px 12px; color:#2E7D32;">Category</th>
              <th style="padding:10px 12px; color:#2E7D32;">Units Sold</th>
              <th style="padding:10px 12px; color:#2E7D32;">Revenue</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($topSeeds as $i => $seed): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px; font-weight:700; color:<?= $i < 3 ? '#FFC107' : '#888' ?>;"><?= $i + 1 ?></td>
              <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($seed['name']) ?></td>
              <td style="padding:10px 12px; color:#555;"><?= htmlspecialchars($seed['category'] ?? '—') ?></td>
              <td style="padding:10px 12px; font-weight:600; color:#2E7D32;"><?= (int)$seed['total_sold'] ?></td>
              <td style="padding:10px 12px; font-weight:600; color:#2E7D32;">₱<?= number_format($seed['revenue'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <!-- ACTIVITY LOG -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>📋 Recent Activity</h2>
        <span style="font-size:12px; color:#888;">Last 20 entries</span>
      </div>
      <?php if (empty($activityLogs)): ?>
        <p style="color:#888; font-size:13px; text-align:center; padding:24px 0;">No activity logs yet.</p>
      <?php else: ?>
      <div style="display:flex; flex-direction:column; gap:8px;">
        <?php foreach ($activityLogs as $log): ?>
        <div style="display:flex; align-items:center; gap:14px; padding:10px 14px; background:#fafff8; border:1px solid #e8f5e9; border-radius:8px; font-size:13px;">
          <span style="font-size:18px;">📝</span>
          <div style="flex:1;">
            <span style="font-weight:500; color:#333;"><?= htmlspecialchars($log['action']) ?></span>
            <?php if (!empty($log['first_name'])): ?>
              <span style="color:#888;"> — <?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></span>
            <?php endif; ?>
          </div>
          <span style="font-size:11px; color:#aaa; flex-shrink:0;"><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle Admin.</p>
</footer>

</body>
</html>
