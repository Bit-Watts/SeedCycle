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
<aside class="sc-sidebar">
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
      <a href="<?= $item['href'] ?>" class="sc-sidebar-link<?= $_activePage === $key ? ' active' : '' ?>">
        <?= $item['icon'] ?> <?= $item['label'] ?>
      </a>
    <?php endforeach; ?>
    <a href="logout.php" class="sc-sidebar-link sc-sidebar-logout">
      <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
  </nav>
</aside>
