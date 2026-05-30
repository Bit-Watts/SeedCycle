<?php
/**
 * Forgot Password
 *
 * Flow:
 *  1. User enters email → token saved to DB → reset link emailed via Mailtrap
 *  2. User clicks link  → enters new password → token marked used → redirect to login
 */

// Set correct timezone for the Philippines
date_default_timezone_set('Asia/Manila');

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Helpers/Mailer.php';

global $conn;
$userModel = new User($conn);

$step  = 'request';
$error = null;
$success = null;
$token = trim($_GET['token'] ?? '');

// ── STEP 2: token in URL → show new-password form ──────────────────────────
if ($token !== '') {
    $step = 'reset';

    $stmt = mysqli_prepare($conn,
        'SELECT * FROM password_resets
         WHERE token = ? AND used = 0 AND expires_at > NOW()
         LIMIT 1'
    );
    mysqli_stmt_bind_param($stmt, 's', $token);
    mysqli_stmt_execute($stmt);
    $resetRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if (!$resetRow) {
        $step  = 'request';
        $error = 'This reset link is invalid or has expired. Please request a new one.';
        $token = '';
    }

    if ($resetRow && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $newPass     = $_POST['password']         ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (strlen($newPass) < 6) {
            $error = 'Password must be at least 6 characters.';
        } elseif ($newPass !== $confirmPass) {
            $error = 'Passwords do not match.';
        } else {
            $user = $userModel->findByEmail($resetRow['email']);
            if ($user) {
                $userModel->updatePassword((int)$user['id'], $newPass);

                $stmt2 = mysqli_prepare($conn,
                    'UPDATE password_resets SET used = 1 WHERE token = ?'
                );
                mysqli_stmt_bind_param($stmt2, 's', $token);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);

                $step    = 'done';
                $success = 'Your password has been reset. You can now log in.';
            } else {
                $error = 'Account not found.';
            }
        }
    }
}

// ── STEP 1: request form POST ───────────────────────────────────────────────
if ($step === 'request' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $user = $userModel->findByEmail($email);

        if ($user) {
            // Remove old tokens for this email
            $del = mysqli_prepare($conn, 'DELETE FROM password_resets WHERE email = ?');
            mysqli_stmt_bind_param($del, 's', $email);
            mysqli_stmt_execute($del);
            mysqli_stmt_close($del);

            // Create new token — expires in 24 hours, expiry set by MySQL to avoid timezone issues
            $newToken = bin2hex(random_bytes(32));

            $ins = mysqli_prepare($conn,
                'INSERT INTO password_resets (email, token, expires_at)
                 VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR))'
            );
            mysqli_stmt_bind_param($ins, 'ss', $email, $newToken);
            mysqli_stmt_execute($ins);
            mysqli_stmt_close($ins);

            // Build reset link
            $resetLink = 'http://' . $_SERVER['HTTP_HOST']
                       . '/SeedCycle/public/forgot-password.php?token=' . $newToken;

            // Build HTML email
            $firstName = htmlspecialchars($user['first_name']);
            $htmlBody  = '
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <style>
    body { font-family: Inter, Arial, sans-serif; background:#f5fbef; margin:0; padding:0; }
    .wrap { max-width:520px; margin:40px auto; background:#fff; border-radius:16px;
            border:1px solid #c8e6c9; overflow:hidden; }
    .header { background:#2E7D32; padding:28px 32px; text-align:center; }
    .header h1 { color:#fff; margin:0; font-size:22px; font-family:Poppins,sans-serif; }
    .header span { color:#FFC107; }
    .body { padding:32px; color:#333; line-height:1.7; font-size:14px; }
    .body h2 { color:#2E7D32; font-size:18px; margin-bottom:8px; }
    .btn { display:inline-block; margin:24px 0; padding:14px 32px;
           background:#4CAF50; color:#fff; border-radius:10px;
           text-decoration:none; font-weight:600; font-size:15px; }
    .note { font-size:12px; color:#888; margin-top:16px; }
    .footer { background:#f5fbef; padding:16px 32px; text-align:center;
              font-size:11px; color:#aaa; border-top:1px solid #e8f5e9; }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header">
      <h1>Seed<span>Cycle</span></h1>
    </div>
    <div class="body">
      <h2>Hi ' . $firstName . ', reset your password 🔑</h2>
      <p>We received a request to reset the password for your SeedCycle account.</p>
      <p>Click the button below to set a new password. This link expires in <strong>24 hours</strong>.</p>
      <a href="' . $resetLink . '" class="btn">Reset My Password</a>
      <p>If the button doesn\'t work, copy and paste this link into your browser:</p>
      <p style="word-break:break-all;color:#4CAF50;font-size:12px;">' . $resetLink . '</p>
      <p class="note">If you didn\'t request a password reset, you can safely ignore this email. Your password will not change.</p>
    </div>
    <div class="footer">© 2026 SeedCycle. All rights reserved.</div>
  </div>
</body>
</html>';

            $sent = Mailer::send(
                $email,
                $user['first_name'] . ' ' . $user['last_name'],
                'Reset your SeedCycle password',
                $htmlBody
            );

            if ($sent) {
                $success = 'A password reset link has been sent to <strong>' . htmlspecialchars($email) . '</strong>. Check your inbox (and spam folder).';
            } else {
                $error = 'Failed to send the reset email. Please try again later.';
            }
        } else {
            // Same message even if email not found — prevents enumeration
            $success = 'If that email is registered, a reset link has been sent to it.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SeedCycle - Forgot Password</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<nav class="sc-nav">
  <a href="landing.php" class="sc-logo">Seed<span>Cycle</span></a>
  <a href="login.php" class="sc-btn-nav">Login</a>
</nav>

<div class="sc-auth-page">
  <div class="sc-auth-card sc-login-card">

    <?php if ($step === 'done'): ?>
      <!-- ── SUCCESS STATE ── -->
      <div style="text-align:center; padding:10px 0 20px;">
        <div style="font-size:48px; margin-bottom:12px;">✅</div>
        <h2>Password Reset!</h2>
        <p style="margin-bottom:24px;">Your password has been updated successfully.</p>
        <a href="login.php" style="display:block; text-align:center; padding:13px;
           background:#4CAF50; color:#fff; border-radius:14px; font-weight:600; text-decoration:none;">
          Go to Login
        </a>
      </div>

    <?php elseif ($step === 'reset'): ?>
      <!-- ── STEP 2: New password form ── -->
      <h2>Set New Password 🔑</h2>
      <p>Enter a new password for your account.</p>

      <?php if ($error): ?>
        <div class="sc-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="forgot-password.php?token=<?= htmlspecialchars($token) ?>">
        <div class="sc-form-group">
          <label>New Password</label>
          <input type="password" name="password" placeholder="••••••••" minlength="6" required autofocus>
        </div>
        <div class="sc-form-group">
          <label>Confirm New Password</label>
          <input type="password" name="confirm_password" placeholder="••••••••" minlength="6" required>
        </div>
        <button type="submit" class="sc-btn-login">Reset Password</button>
      </form>

    <?php else: ?>
      <!-- ── STEP 1: Request form ── -->
      <h2>Forgot Password? 🔒</h2>
      <p>Enter your email and we'll send you a reset link.</p>

      <?php if ($error): ?>
        <div class="sc-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="sc-success" style="line-height:1.6;"><?= $success ?></div>
      <?php endif; ?>

      <?php if (!$success): ?>
      <form method="POST" action="forgot-password.php">
        <div class="sc-form-group">
          <label>Email Address</label>
          <input type="email" name="email" placeholder="you@email.com"
                 value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
        </div>
        <button type="submit" class="sc-btn-login">
          <i class="fa-solid fa-paper-plane"></i> Send Reset Link
        </button>
      </form>
      <?php endif; ?>

    <?php endif; ?>

    <div class="sc-divider">or</div>
    <p class="sc-signup-text" style="text-align:center;">
      Remember your password? <a href="login.php">Login</a>
    </p>

  </div>
</div>

<footer class="sc-footer">
  <p>© 2026 SeedCycle. All rights reserved.</p>
</footer>

</body>
</html>
