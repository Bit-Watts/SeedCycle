<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php">Marketplace</a></li>
    <li><a href="planting-guide.php">Planting Guide</a></li>
  </ul>
  <a href="login.php"><button class="sc-btn-nav">Login</button></a>
</nav>

<div class="sc-page">
  <div class="sc-login-card">
    <h2>Welcome Back!</h2>
    <p>Login to your SeedCycle account</p>

    <?php if (isset($error)): ?>
      <div class="sc-error"><?= $error ?></div>
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
      <a href="#" class="sc-forgot">Forgot password?</a>
      <button type="submit" class="sc-btn-login">Login</button>
    </form>

    <div class="sc-divider">or</div>
    <p class="sc-signup-text">Don't have an account? <a href="signup.php">Sign Up</a></p>
  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
  <div class="sc-footer-links">
    <a href="index.php">Home</a>
    <a href="marketplace.php">Marketplace</a>
    <a href="planting-guide.php">Planting Guide</a>
  </div>
</footer>

</body>
</html>
