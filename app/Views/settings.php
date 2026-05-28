<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/settings.css">
</head>
<body>

<?php require __DIR__ . '/includes/navbar.php'; ?>

<div class="sc-dashboard">

  <?php $activePage = 'settings'; require __DIR__ . '/includes/sidebar.php'; ?>

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
              <i class="fa-solid fa-seedling" style="font-size:28px; color:#4CAF50;"></i>
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

<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>

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
</script>
</body>
</html>
