<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Verify 2FA</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css">
  <link rel="stylesheet" href="assets/css/two-factor.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
</nav>

<div class="sc-2fa-page">
  <div class="sc-2fa-card">

    <div style="text-align:center; margin-bottom:20px;">
      <div style="font-size:48px; margin-bottom:12px;">🔐</div>
      <h2>Two-Factor Verification</h2>
      <p>Enter the 6-digit code from your Google Authenticator app to continue.</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="sc-2fa-alert error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="two-factor-verify.php">
      <label style="display:block; font-size:13px; font-weight:500; color:#444; margin-bottom:8px; text-align:center;">
        Authenticator Code
      </label>
      <input type="text" name="code" class="sc-code-input" maxlength="6" pattern="\d{6}"
             placeholder="000000" autocomplete="one-time-code" autofocus required>
      <button type="submit" class="sc-btn-2fa-primary">Verify & Login</button>
    </form>

    <hr class="sc-2fa-divider">

    <a href="login.php" class="sc-btn-2fa-secondary" style="display:block; text-align:center; text-decoration:none; padding:11px; border-radius:10px; border:1.5px solid #e0e0e0; color:#888; font-size:14px;">
      ← Back to Login
    </a>

  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

</body>
</html>
