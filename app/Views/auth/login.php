<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<nav class="sc-nav">
  <a href="landing.php" class="sc-logo"><img src="assets/images/SeedCycleLogo.png" alt="SeedCycle" class="sc-logo-img"> Seed<span>Cycle</span></a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="sc-nav-user">
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?></span>
      <a href="index.php" class="sc-btn-nav">Dashboard</a>
      <a href="logout.php" class="sc-btn-nav">Logout</a>
    </div>
  <?php else: ?>
    <a href="signup.php" class="sc-btn-nav">Sign Up</a>
  <?php endif; ?>
</nav>

<div class="sc-auth-page">
  <div class="sc-auth-card sc-login-card">
    <h2>Welcome Back 👋</h2>
    <p>Login to your SeedCycle account</p>

    <?php if (isset($error)): ?>
      <div class="sc-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="POST">
      <div class="sc-form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@email.com" required>
      </div>
      <div class="sc-form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <a href="forgot-password.php" class="sc-forgot">Forgot password?</a>
      <button type="submit" class="sc-btn-login">Login</button>
    </form>

    <div class="sc-divider">or</div>
    <p class="sc-signup-text">Don't have an account? <a href="signup.php">Sign Up</a></p>
  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

</body>
</html>
