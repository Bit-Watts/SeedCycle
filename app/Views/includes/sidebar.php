<?php
/**
 * Shared sidebar for all authenticated user pages.
 * Expects: $user array, $activePage string (e.g. 'dashboard', 'marketplace')
 */
$_pi          = $user['profile_image'] ?? $_SESSION['profile_image'] ?? '';
$_firstName   = htmlspecialchars($user['first_name'] ?? 'Grower');
$_email       = htmlspecialchars($user['email'] ?? '');
$_activePage  = $activePage ?? '';

$_navItems = [
    'dashboard'         => ['href' => 'index.php',            'icon' => '<i class="fa-solid fa-chart-line"></i>',           'label' => 'Overview'],
    'my-seeds'          => ['href' => 'my-seeds.php',         'icon' => '<i class="fa-solid fa-wheat-awn"></i>',            'label' => 'My Seeds'],
    'sell-seeds'        => ['href' => 'sell-seeds.php',       'icon' => '<i class="fa-solid fa-plus"></i>',                 'label' => 'Sell Seeds'],
    'seller-orders'     => ['href' => 'seller-orders.php',    'icon' => '<i class="fa-solid fa-truck-fast"></i>',           'label' => 'Seller Orders'],
    'marketplace'       => ['href' => 'marketplace.php',      'icon' => '<i class="fa-solid fa-store"></i>',                'label' => 'Marketplace'],
    'planting-guide'    => ['href' => 'planting-guide.php',   'icon' => '<i class="fa-solid fa-calendar-days"></i>',        'label' => 'Planting Guide'],
    'orders'            => ['href' => 'orders.php',           'icon' => '<i class="fa-solid fa-bag-shopping"></i>',         'label' => 'My Orders'],
    'settings'          => ['href' => 'settings.php',         'icon' => '<i class="fa-solid fa-gear"></i>',                 'label' => 'Settings'],
];
?>
<aside class="sc-sidebar sc-sidebar-collapsible" id="mainSidebar">
  <div class="sc-sidebar-avatar">
    <div class="sc-avatar" style="overflow:hidden;">
      <?php if (!empty($_pi)): ?>
        <img src="<?= htmlspecialchars($_pi) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
      <?php else: ?>
        <i class="fa-solid fa-seedling" style="font-size:28px; color:#4CAF50;"></i>
      <?php endif; ?>
    </div>
    <p class="sc-sidebar-name"><?= $_firstName ?></p>
    <p class="sc-sidebar-email"><?= $_email ?></p>
  </div>
  <nav class="sc-sidebar-nav">
    <?php foreach ($_navItems as $key => $item): ?>
      <a href="<?= $item['href'] ?>"
         class="sc-sidebar-link<?= $_activePage === $key ? ' active' : '' ?>"
         title="<?= htmlspecialchars(strip_tags($item['label'])) ?>">
        <span class="sc-sidebar-icon"><?= $item['icon'] ?></span>
        <span class="sc-sidebar-label"><?= $item['label'] ?></span>
      </a>
    <?php endforeach; ?>
    <a href="logout.php" class="sc-sidebar-link sc-sidebar-logout" title="Logout">
      <span class="sc-sidebar-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
      <span class="sc-sidebar-label">Logout</span>
    </a>
  </nav>
</aside>

<style>
/* ── COLLAPSIBLE SIDEBAR ── */
.sc-sidebar-collapsible {
  width: 58px;
  transition: width 0.25s ease;
  overflow: hidden;
  white-space: nowrap;
  min-width: 58px;
}

.sc-sidebar-collapsible .sc-sidebar-nav {
  width: 230px;
}

.sc-sidebar-collapsible:hover,
.sc-sidebar-collapsible.sc-sidebar--expanded {
  width: 230px;
}

/* Hide avatar text and email when collapsed */
.sc-sidebar-collapsible .sc-sidebar-name,
.sc-sidebar-collapsible .sc-sidebar-email {
  opacity: 0;
  transition: opacity 0.2s ease;
  overflow: hidden;
}

.sc-sidebar-collapsible:hover .sc-sidebar-name,
.sc-sidebar-collapsible:hover .sc-sidebar-email,
.sc-sidebar-collapsible.sc-sidebar--expanded .sc-sidebar-name,
.sc-sidebar-collapsible.sc-sidebar--expanded .sc-sidebar-email {
  opacity: 1;
}

/* Avatar center when collapsed */
.sc-sidebar-collapsible .sc-sidebar-avatar {
  display: none;
}

.sc-sidebar-collapsible:hover .sc-sidebar-avatar,
.sc-sidebar-collapsible.sc-sidebar--expanded .sc-sidebar-avatar {
  display: flex;
  flex-direction: column;
  align-items: center;
}

/* Shrink avatar when collapsed */
.sc-sidebar-collapsible .sc-avatar {
  width: 52px;
  height: 52px;
  font-size: 22px;
}

/* Link layout */
.sc-sidebar-collapsible .sc-sidebar-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  border-radius: var(--radius-md);
  overflow: hidden;
  width: 100%;
  box-sizing: border-box;
}

.sc-sidebar-collapsible .sc-sidebar-icon {
  flex-shrink: 0;
  width: 22px;
  text-align: center;
  font-size: 15px;
}

.sc-sidebar-collapsible .sc-sidebar-label {
  opacity: 0;
  transition: opacity 0.2s ease;
  white-space: nowrap;
}

.sc-sidebar-collapsible:hover .sc-sidebar-label,
.sc-sidebar-collapsible.sc-sidebar--expanded .sc-sidebar-label {
  opacity: 1;
}

/* Active item always shows label */
.sc-sidebar-collapsible .sc-sidebar-link.active .sc-sidebar-label {
  opacity: 1;
}

/* On mobile — sidebar is full overlay, ignore collapsible */
@media (max-width: 768px) {
  .sc-sidebar-collapsible {
    width: 270px !important;
    white-space: normal !important;
  }
  .sc-sidebar-collapsible .sc-sidebar-label,
  .sc-sidebar-collapsible .sc-sidebar-name,
  .sc-sidebar-collapsible .sc-sidebar-email {
    opacity: 1 !important;
  }
}
</style>
