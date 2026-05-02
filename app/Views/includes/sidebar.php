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
    'dashboard'     => ['href' => 'index.php',         'icon' => '📊', 'label' => 'Overview'],
    'my-seeds'      => ['href' => 'my-seeds.php',      'icon' => '🌾', 'label' => 'My Seeds'],
    'sell-seeds'    => ['href' => 'sell-seeds.php',    'icon' => '➕', 'label' => 'Sell Seeds'],
    'seller-orders' => ['href' => 'seller-orders.php', 'icon' => '📬', 'label' => 'Seller Orders'],
    'marketplace'   => ['href' => 'marketplace.php',   'icon' => '🛒', 'label' => 'Marketplace'],
    'planting-guide'=> ['href' => 'planting-guide.php','icon' => '📅', 'label' => 'Planting Guide'],
    'orders'        => ['href' => 'orders.php',        'icon' => '📦', 'label' => 'My Orders'],
    'settings'      => ['href' => 'settings.php',      'icon' => '⚙️', 'label' => 'Settings'],
];
?>
<aside class="sc-sidebar">
  <div class="sc-sidebar-avatar">
    <div class="sc-avatar" style="overflow:hidden;">
      <?php if (!empty($_pi)): ?>
        <img src="<?= htmlspecialchars($_pi) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
      <?php else: ?>
        🌱
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
  </nav>
</aside>
