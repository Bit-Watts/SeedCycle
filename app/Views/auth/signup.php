<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/singup.css">
</head>
<body>

<nav class="sc-nav">
  <div class="sc-logo">Seed<span>Cycle</span></div>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php">Marketplace</a></li>
    <li><a href="planting-guide.php">Planting Guide</a></li>
  </ul>
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
  <div class="sc-signup-card">
    <h2>Create an Account</h2>
    <p>Join SeedCycle and start growing smarter</p>

    <?php if (isset($error)): ?>
      <div class="sc-error"><?= $error ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div class="sc-success"><?= $success ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
      <!-- PROFILE IMAGE -->
      <div class="sc-form-group" style="text-align:center; margin-bottom:20px;">
        <div id="avatar-preview" style="width:80px; height:80px; border-radius:50%; background:#e8f5e9; border:2px solid #c8e6c9; display:flex; align-items:center; justify-content:center; font-size:36px; margin:0 auto 10px; overflow:hidden; cursor:pointer;" onclick="document.getElementById('profile_image').click()">
          🌱
        </div>
        <small style="font-size:11px; color:#aaa;">Click to upload profile photo</small>
        <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display:none;" onchange="previewAvatar(event)">
      </div>

      <div class="sc-form-row">
        <div class="sc-form-group">
          <label>First Name</label>
          <input type="text" name="first_name" placeholder="Juan" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
        </div>
        <div class="sc-form-group">
          <label>Last Name</label>
          <input type="text" name="last_name" placeholder="Dela Cruz" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
        </div>
      </div>
      <div class="sc-form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="you@email.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
      </div>
      <div class="sc-form-group">
        <label>Username</label>
        <input type="text" name="username" placeholder="juandelacruz" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
      </div>
      <div class="sc-form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone_number" placeholder="e.g. 09123456789" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>">
      </div>
      <div class="sc-form-group">
        <label>Address</label>
        <input type="text" name="address" placeholder="Street, Barangay, City" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
      </div>
      <div class="sc-form-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <div class="sc-form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="sc-btn-signup">Create Account</button>
    </form>

    <div class="sc-divider">or</div>
    <p class="sc-login-text">Already have an account? <a href="login.php">Login</a></p>
  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

<script>
  function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview = document.getElementById('avatar-preview');
    preview.innerHTML = '';
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.style.cssText = 'width:100%; height:100%; object-fit:cover; border-radius:50%;';
    preview.appendChild(img);
  }
</script>
</body>
</html>
