<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Two-Factor Authentication</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/two-factor.css">
</head>
<body>

<nav class="sc-nav">
  <a href="<?= $isLoginFlow ? 'login.php' : 'index.php' ?>" class="sc-logo">Seed<span>Cycle</span></a>
  <?php if (!$isLoginFlow): ?>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($user['first_name'] ?? 'Grower') ?> 👋</span>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
  <?php endif; ?>
</nav>

<?php if ($isLoginFlow): ?>
<!-- LOGIN FLOW: centered card, no sidebar -->
<div class="sc-2fa-page">
  <div class="sc-2fa-card">
<?php else: ?>
<!-- SETTINGS FLOW: dashboard layout with sidebar -->
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
      <a href="seller-orders.php" class="sc-sidebar-link">📬 Seller Orders</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">📦 My Orders</a>
      <a href="settings.php" class="sc-sidebar-link">⚙️ Settings</a>
      <a href="two-factor-setup.php" class="sc-sidebar-link active">🔐 2FA Security</a>
    </nav>
  </aside>
  <main class="sc-main">
    <div class="sc-main-header">
      <h1>🔐 Two-Factor Authentication</h1>
      <p>Add an extra layer of security to your account.</p>
    </div>
  <div class="sc-2fa-card" style="max-width:520px;">
<?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="sc-2fa-alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
      <div class="sc-2fa-alert success"><?= $success ?></div>
    <?php endif; ?>

    <?php if ($isLoginFlow && !$status['totp_enabled']): ?>
      <!-- Login flow header -->
      <div style="text-align:center; margin-bottom:20px;">
        <div style="font-size:48px; margin-bottom:8px;">🔐</div>
        <h2>Secure Your Account</h2>
        <p>Set up two-factor authentication to complete your login and protect your account.</p>
      </div>
    <?php else: ?>
      <!-- Status badge (settings page) -->
      <div class="sc-2fa-status <?= $status['totp_enabled'] ? 'enabled' : 'disabled' ?>">
        <?= $status['totp_enabled'] ? '✅ 2FA is ENABLED' : '⚠️ 2FA is DISABLED' ?>
      </div>
    <?php endif; ?>

    <?php if (!$status['totp_enabled']): ?>

      <?php if (!$qrCode): ?>
      <!-- Step 1: Generate -->
      <?php if (!$isLoginFlow): ?><h2>Enable 2FA</h2><?php endif; ?>
      <p>Scan a QR code with <strong>Google Authenticator</strong> to secure your account.</p>
      <form method="POST" action="two-factor-setup.php">
        <input type="hidden" name="action" value="generate">
        <button type="submit" class="sc-btn-2fa-primary">🔑 Generate QR Code</button>
      </form>

      <?php else: ?>
      <!-- Step 2: Show QR + verify -->
      <h2>Scan QR Code</h2>
      <div class="sc-2fa-steps">
        <ol>
          <li>Open <strong>Google Authenticator</strong> on your phone</li>
          <li>Tap <strong>+</strong> → <strong>Scan a QR code</strong></li>
          <li>Scan the QR code below</li>
          <li>Enter the 6-digit code to confirm</li>
        </ol>
      </div>

      <div class="sc-qr-wrap">
        <?= $qrCode ?>
      </div>

      <p style="font-size:12px; color:#888; text-align:center; margin-bottom:4px;">Or enter this key manually:</p>
      <div class="sc-qr-secret"><?= htmlspecialchars($secret) ?></div>

      <form method="POST" action="two-factor-setup.php">
        <input type="hidden" name="action" value="verify_enable">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:8px;">
          Enter 6-digit code from app
        </label>
        <input type="text" name="code" class="sc-code-input" maxlength="6" pattern="\d{6}"
               placeholder="000000" autocomplete="one-time-code" autofocus required>
        <button type="submit" class="sc-btn-2fa-primary">
          ✅ Verify & <?= $isLoginFlow ? 'Login' : 'Enable 2FA' ?>
        </button>
      </form>
      <?php endif; ?>

    <?php else: ?>
      <!-- 2FA enabled — disable option (settings only) -->
      <h2>2FA is Active</h2>
      <p>Your account is protected. To disable 2FA, enter your current authenticator code.</p>
      <form method="POST" action="two-factor-setup.php">
        <input type="hidden" name="action" value="disable">
        <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:8px;">
          Enter 6-digit code to disable
        </label>
        <input type="text" name="code" class="sc-code-input" maxlength="6" pattern="\d{6}"
               placeholder="000000" autocomplete="one-time-code" required>
        <button type="submit" class="sc-btn-2fa-danger"
                onclick="return confirm('Are you sure you want to disable 2FA?')">
          Disable 2FA
        </button>
      </form>
    <?php endif; ?>

    <?php if ($isLoginFlow): ?>
      <hr class="sc-2fa-divider">
      <a href="logout.php" style="display:block; text-align:center; font-size:13px; color:#888; text-decoration:none;">
        ← Cancel and go back to login
      </a>
    <?php endif; ?>

<?php if ($isLoginFlow): ?>
  </div><!-- end sc-2fa-card -->
</div><!-- end sc-2fa-page -->
<?php else: ?>
  </div><!-- end sc-2fa-card -->
  </main>
</div><!-- end sc-dashboard -->
<?php endif; ?>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<?php if (!$isLoginFlow): ?>
<!-- LOGOUT MODAL (settings flow only) -->
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
<?php endif; ?>

</body>
</html>
