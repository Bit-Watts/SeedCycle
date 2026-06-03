<?php
/**
 * Shared navbar for all authenticated user pages.
 * Expects: $user array (optional, falls back to $_SESSION)
 */
$_navName   = $user['first_name'] ?? $_SESSION['first_name'] ?? 'Grower';
$_navUserId = $user['id'] ?? $_SESSION['user_id'] ?? 0;

// Unread notification count
$_unreadCount = 0;
if ($_navUserId && isset($conn)) {
    $stmtN = mysqli_prepare($conn,
        'SELECT COUNT(*) AS cnt FROM order_notifications WHERE user_id = ? AND is_read = 0'
    );
    if ($stmtN) {
        mysqli_stmt_bind_param($stmtN, 'i', $_navUserId);
        mysqli_stmt_execute($stmtN);
        $rowN = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtN));
        mysqli_stmt_close($stmtN);
        $_unreadCount = (int)($rowN['cnt'] ?? 0);
    }
}
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/mobile-optimizations.css">
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#2E7D32">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="SeedCycle">
<link rel="apple-touch-icon" href="assets/images/icon-192x192.png">

<nav class="sc-nav" data-user-id="<?= $_navUserId ?>">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_navName) ?></span>
    <a href="notifications.php" class="sc-nav-icon" title="Notifications" style="position: relative;">
      <i class="fa-solid fa-bell"></i>
      <?php if ($_unreadCount > 0): ?>
        <span class="notification-badge" style="position:absolute;top:-4px;right:-4px;background:#e53935;color:#fff;font-size:10px;font-weight:700;min-width:16px;height:16px;border-radius:8px;display:flex;align-items:center;justify-content:center;padding:0 3px;">
          <?= $_unreadCount > 99 ? '99+' : $_unreadCount ?>
        </span>
      <?php else: ?>
        <span class="notification-badge" style="display:none;">0</span>
      <?php endif; ?>
    </a>
    <a href="chat.php" class="sc-nav-icon" title="Messages" style="position: relative;">
      <i class="fa-solid fa-comments"></i>
    </a>
    <a href="cart.php" class="sc-nav-icon" title="Cart"><i class="fa-solid fa-cart-shopping"></i></a>
    <a href="profile.php" class="sc-nav-icon" title="Profile"><i class="fa-solid fa-user"></i></a>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<!-- WebSocket Client -->
<script src="assets/js/websocket-client.js"></script>

<!-- PWA Installation -->
<script>
// Register service worker
if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/public/service-worker.js')
      .then(registration => {
        console.log('Service Worker registered:', registration);
      })
      .catch(error => {
        console.log('Service Worker registration failed:', error);
      });
  });
}

// PWA install prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;
  // Show install button if needed
  console.log('PWA install available');
});
</script>
