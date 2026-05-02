<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard.css">
  <link rel="stylesheet" href="assets/css/settings.css">
</head>
<body>
<nav class="sc-nav">
  <a href="index.php" class="sc-logo">Seed<span>Cycle</span></a>
  <div class="sc-nav-user">
    <span class="sc-nav-greeting">Hi, <?= htmlspecialchars($_SESSION['first_name'] ?? $user['first_name'] ?? 'Grower') ?> 👋</span>
    <a href="cart.php" class="sc-nav-icon" title="Cart">🛒</a>
    <a href="profile.php" class="sc-nav-icon" title="Profile">👤</a>
    <a href="logout.php"><button class="sc-btn-nav">Logout</button></a>
  </div>
</nav>

<div class="sc-dashboard">

  <aside class="sc-sidebar">
    <div class="sc-sidebar-avatar">
      <div class="sc-avatar" style="overflow:hidden;">
        <?php if (!empty($user['profile_image'])): ?>
          <img src="<?= htmlspecialchars($user['profile_image']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
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
      <a href="seller-orders.php" class="sc-sidebar-link">📦 To Ship</a>
      <a href="marketplace.php" class="sc-sidebar-link">🛒 Marketplace</a>
      <a href="planting-guide.php" class="sc-sidebar-link">📅 Planting Guide</a>
      <a href="orders.php" class="sc-sidebar-link">🛍️ My Orders</a>
      <a href="settings.php" class="sc-sidebar-link active">⚙️ Settings</a>
    </nav>
  </aside>

  <main class="sc-main">

    <div class="sc-main-header">
      <h1>Settings</h1>
      <p>Update your account information.</p>
    </div>

    <?php if (isset($error)): ?>
      <div style="background:#fce4ec; color:#c62828; padding:12px 16px; border-radius:8px; font-size:13px;"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($success)): ?>
      <div style="background:#e8f5e9; color:#2E7D32; padding:12px 16px; border-radius:8px; font-size:13px;"><?= $success ?></div>
    <?php endif; ?>

    <!-- PROFILE INFO -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Profile Information</h2>
      </div>
      <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="action" value="update_profile">

        <!-- PROFILE IMAGE -->
        <div style="display:flex; align-items:center; gap:20px; margin-bottom:20px; padding-bottom:20px; border-bottom:1px solid #e8f5e9;">
          <div id="avatar-preview" onclick="document.getElementById('profile_image_input').click()"
            style="width:72px; height:72px; border-radius:50%; background:#e8f5e9; border:2px solid #c8e6c9;
                   display:flex; align-items:center; justify-content:center; font-size:30px;
                   overflow:hidden; cursor:pointer; flex-shrink:0;">
            <?php if (!empty($user['profile_image'])): ?>
              <img src="<?= htmlspecialchars($user['profile_image']) ?>" style="width:100%; height:100%; object-fit:cover;">
            <?php else: ?>
              🌱
            <?php endif; ?>
          </div>
          <div>
            <p style="font-size:13px; font-weight:500; color:#333; margin-bottom:4px;">Profile Photo</p>
            <p style="font-size:11px; color:#aaa; margin-bottom:8px;">JPG, PNG, WEBP — max 2MB</p>
            <button type="button" onclick="document.getElementById('profile_image_input').click()"
              style="background:#e8f5e9; color:#2E7D32; border:none; padding:6px 14px; border-radius:6px; font-size:12px; cursor:pointer; font-family:inherit;">
              Change Photo
            </button>
          </div>
          <input type="file" id="profile_image_input" name="profile_image" accept="image/*" style="display:none;" onchange="previewSettingsAvatar(event)">
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">First Name <span style="color:#e53935;">*</span></label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required
              style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
          </div>
          <div>
            <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Last Name <span style="color:#e53935;">*</span></label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required
              style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
          </div>
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Username <span style="color:#e53935;">*</span></label>
          <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
            style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Email Address <span style="color:#e53935;">*</span></label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
            style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
        </div>
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Phone Number</label>
          <input type="tel" name="phone_number" value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>"
            placeholder="e.g. 09123456789"
            style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
        </div>
        <div style="margin-bottom:20px;">
          <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Address</label>
          <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>"
            placeholder="Street, Barangay, City"
            style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
        </div>
        <button type="submit"
          style="background:#2E7D32; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; font-family:inherit;">
          Save Changes
        </button>
      </form>
    </div>

    <!-- CHANGE PASSWORD -->
    <div class="sc-section">
      <div class="sc-section-header">
        <h2>Change Password</h2>
      </div>
      <form method="POST" action="">
        <input type="hidden" name="action" value="change_password">
        <div style="margin-bottom:16px;">
          <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Current Password</label>
          <input type="password" name="current_password" required
            style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">
          <div>
            <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">New Password</label>
            <input type="password" name="new_password" required
              style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
          </div>
          <div>
            <label style="display:block; font-size:12px; font-weight:500; color:#555; margin-bottom:6px;">Confirm New Password</label>
            <input type="password" name="confirm_password" required
              style="width:100%; padding:10px 12px; border:1px solid #c8e6c9; border-radius:8px; font-size:13px; font-family:inherit;">
          </div>
        </div>
        <button type="submit"
          style="background:#2E7D32; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; font-family:inherit;">
          Update Password
        </button>
      </form>
    </div>

  </main>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>


<!-- LOGOUT CONFIRMATION MODAL -->
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
  function previewSettingsAvatar(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview = document.getElementById('avatar-preview');
    preview.innerHTML = '';
    const img = document.createElement('img');
    img.src = URL.createObjectURL(file);
    img.style.cssText = 'width:100%; height:100%; object-fit:cover; border-radius:50%;';
    preview.appendChild(img);
  }

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('a[href="logout.php"]').forEach(function(el) {
      el.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('logoutOverlay').classList.add('active');
      });
    });
  });
</script>
</body>
</html>
