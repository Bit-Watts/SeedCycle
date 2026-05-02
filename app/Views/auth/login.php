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
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="sc-nav-user">
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?> 👋</span>
      <a href="index.php"><button class="sc-btn-nav">Dashboard</button></a>
      <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
    </div>
  <?php else: ?>
    <a href="login.php"><button class="sc-btn-nav">Login</button></a>
  <?php endif; ?>
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
</footer>

</body>
</html>
