<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Sign Up</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/singup.css">
</head>
<body>

<nav class="sc-nav">
  <a href="landing.php" class="sc-logo"><img src="assets/images/SeedCycleLogo.png" alt="SeedCycle" class="sc-logo-img"> Seed<span>Cycle</span></a>
  <ul class="sc-navlinks">
    <li><a href="index.php">Home</a></li>
    <li><a href="marketplace.php">Marketplace</a></li>
    <li><a href="planting-guide.php">Planting Guide</a></li>
  </ul>
  <?php if (isset($_SESSION['user_id'])): ?>
    <div class="sc-nav-user">
      <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? 'Grower') ?></span>
      <a href="index.php" class="sc-btn-nav">Dashboard</a>
      <a href="logout.php" class="sc-btn-nav">Logout</a>
    </div>
  <?php else: ?>
    <a href="login.php" class="sc-btn-nav">Login</a>
  <?php endif; ?>
</nav>

<div class="sc-auth-page">
  <div class="sc-auth-card sc-signup-card">
    <h2>Create an Account 🌱</h2>
    <p>Join SeedCycle and start growing smarter</p>

    <?php if (isset($error)): ?>
      <div class="sc-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div class="sc-success"><?= $success ?></div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">

      <!-- PROFILE IMAGE -->
      <div class="sc-form-group" style="text-align:center; margin-bottom:20px;">
        <div id="avatar-preview" class="sc-signup-avatar" onclick="document.getElementById('profile_image').click()">
          <i class="fa-solid fa-seedling"></i>
        </div>
        <small style="font-size:11px; color:var(--color-text-light);">Click to upload profile photo</small>
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
        <label>Phone Number <span style="color:#e53e3e;">*</span></label>
        <input type="tel" name="phone_number" placeholder="e.g. 09123456789" value="<?= htmlspecialchars($_POST['phone_number'] ?? '') ?>" required>
      </div>
      <div class="sc-form-group">
        <label>Address <span style="color:#e53e3e;">*</span></label>
        <input type="text" name="address" placeholder="Street, Barangay, City, Province" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" required>
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
