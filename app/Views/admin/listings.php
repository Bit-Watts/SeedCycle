<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle Admin - Listings</title>
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
      <a href="listings.php" class="sc-sidebar-link active">📋 Listings</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="shipments.php" class="sc-sidebar-link">🚚 Shipments</a>
      <a href="reports.php" class="sc-sidebar-link">📈 Reports</a>
      <a href="../index.php" class="sc-sidebar-link" style="margin-top:12px; color:#888;">← User View</a>
    </nav>
  </aside>

  <main class="sc-main">
    <div class="sc-main-header">
      <h1>Seed Listings</h1>
      <p>Review and approve user listing requests.</p>
    </div>

    <?php if (!empty($message)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:10px 16px; border-radius:8px; font-size:13px; border:1px solid #c8e6c9; margin-bottom:16px;"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php
    $grouped = ['pending' => [], 'approved' => [], 'rejected' => []];
    foreach ($listings as $l) {
        $grouped[$l['status'] ?? 'pending'][] = $l;
    }
    $statusLabels = ['pending' => '⏳ Pending', 'approved' => '✅ Approved', 'rejected' => '❌ Rejected'];
    $monthNames   = ['','January','February','March','April','May','June',
                     'July','August','September','October','November','December'];
    ?>

    <?php foreach ($grouped as $status => $items): ?>
    <div class="sc-section">
      <div class="sc-section-header">
        <h2><?= $statusLabels[$status] ?></h2>
        <span style="font-size:12px; color:#888;"><?= count($items) ?> listing<?= count($items) !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($items)): ?>
        <p style="color:#888; font-size:13px; padding:12px 0;">No <?= $status ?> listings.</p>
      <?php else: ?>
      <div class="sc-table-wrap">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
          <thead>
            <tr style="border-bottom:2px solid #e8f5e9; text-align:left;">
              <th style="padding:10px 12px; color:#2E7D32;">Seed</th>
              <th style="padding:10px 12px; color:#2E7D32;">Category</th>
              <th style="padding:10px 12px; color:#2E7D32;">Price</th>
              <th style="padding:10px 12px; color:#2E7D32;">Submitted By</th>
              <th style="padding:10px 12px; color:#2E7D32;">Date</th>
              <th style="padding:10px 12px; color:#2E7D32;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $l): ?>
            <tr style="border-bottom:1px solid #f0f0f0;">
              <td style="padding:10px 12px; font-weight:500;">🌱 <?= htmlspecialchars($l['seed_name'] ?? '—') ?></td>
              <td style="padding:10px 12px; color:#555;"><?= htmlspecialchars($l['category'] ?? '—') ?></td>
              <td style="padding:10px 12px; color:#2E7D32; font-weight:600;">
                <?= $l['price'] ? '₱' . number_format($l['price'], 2) : '—' ?>
              </td>
              <td style="padding:10px 12px;">
                <?= htmlspecialchars($l['first_name'] . ' ' . $l['last_name']) ?><br>
                <span style="font-size:11px; color:#888;"><?= htmlspecialchars($l['email']) ?></span>
              </td>
              <td style="padding:10px 12px; color:#888;"><?= date('M j, Y', strtotime($l['created_at'])) ?></td>
              <td style="padding:10px 12px; display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                <!-- View details popup -->
                <button class="sc-btn-view-detail"
                  onclick="openDetail(<?= htmlspecialchars(json_encode($l)) ?>)">
                  View
                </button>
                <?php if ($status === 'pending'): ?>
                <form method="POST" action="listings.php" style="display:inline;">
                  <input type="hidden" name="listing_id" value="<?= (int)$l['id'] ?>">
                  <input type="hidden" name="action" value="approve">
                  <button type="submit" class="sc-btn-approve" style="padding:5px 12px; font-size:12px;">Approve</button>
                </form>
                <form method="POST" action="listings.php" style="display:inline;">
                  <input type="hidden" name="listing_id" value="<?= (int)$l['id'] ?>">
                  <input type="hidden" name="action" value="reject">
                  <button type="submit" class="sc-btn-reject" style="padding:5px 12px; font-size:12px;"
                    onclick="return confirm('Reject this listing?')">Reject</button>
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
    <?php endforeach; ?>

  </main>
</div>

<!-- LISTING DETAIL POPUP -->
<div class="sc-detail-overlay" id="detailOverlay" onclick="closeDetailOnOverlay(event)">
  <div class="sc-detail-popup" id="detailPopup">
    <button class="sc-close-btn" onclick="closeDetail()">✕</button>
    <h3 id="dp-title">Seed Details</h3>
    <img id="dp-image" src="" alt="" style="display:none;">
    <div id="dp-rows"></div>
    <div class="sc-detail-actions" id="dp-actions"></div>
  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle Admin.</p>
</footer>

<script>
  const monthNames = ['','January','February','March','April','May','June',
                      'July','August','September','October','November','December'];

  function openDetail(listing) {
    document.getElementById('dp-title').textContent = '🌱 ' + (listing.seed_name || '—');

    // Image
    const img = document.getElementById('dp-image');
    if (listing.image_url) {
      img.src = '../' + listing.image_url;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }

    // Info rows
    const startM = listing.planting_start_month ? monthNames[listing.planting_start_month] : '—';
    const endM   = listing.planting_end_month   ? monthNames[listing.planting_end_month]   : '—';
    const rows = [
      ['Category',       listing.category    || '—'],
      ['Price',          listing.price ? '₱' + parseFloat(listing.price).toFixed(2) : '—'],
      ['Planting Start', startM],
      ['Planting End',   endM],
      ['Growing Days',   listing.growing_days ? listing.growing_days + ' days' : '—'],
      ['Description',    listing.description || '—'],
      ['Submitted By',   listing.first_name + ' ' + listing.last_name],
      ['Email',          listing.email],
      ['Status',         listing.status ? listing.status.charAt(0).toUpperCase() + listing.status.slice(1) : '—'],
      ['Date',           new Date(listing.created_at).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'})],
    ];

    const rowsHtml = rows.map(([label, val]) =>
      `<div class="sc-detail-row">
        <span class="sc-detail-label">${label}</span>
        <span class="sc-detail-value">${val}</span>
      </div>`
    ).join('');
    document.getElementById('dp-rows').innerHTML = rowsHtml;

    // Actions (only for pending)
    const actions = document.getElementById('dp-actions');
    if (listing.status === 'pending') {
      actions.innerHTML = `
        <form method="POST" action="listings.php" style="flex:1;">
          <input type="hidden" name="listing_id" value="${listing.id}">
          <input type="hidden" name="action" value="approve">
          <button type="submit" class="sc-btn-approve" style="width:100%;">✅ Approve</button>
        </form>
        <form method="POST" action="listings.php" style="flex:1;" onsubmit="return confirm('Reject this listing?')">
          <input type="hidden" name="listing_id" value="${listing.id}">
          <input type="hidden" name="action" value="reject">
          <button type="submit" class="sc-btn-reject" style="width:100%;">❌ Reject</button>
        </form>`;
    } else {
      actions.innerHTML = '';
    }

    document.getElementById('detailOverlay').classList.add('active');
  }

  function closeDetail() {
    document.getElementById('detailOverlay').classList.remove('active');
  }

  function closeDetailOnOverlay(e) {
    if (e.target === document.getElementById('detailOverlay')) closeDetail();
  }
</script>

</body>
</html>
