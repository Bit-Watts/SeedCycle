<?php
/**
 * Mail configuration — copy this file to Mail.php and fill in your credentials.
 *
 * Gmail SMTP setup:
 *  1. Enable 2-Step Verification on your Google account
 *  2. Go to myaccount.google.com → search "App Passwords"
 *  3. Create one named "SeedCycle" → copy the 16-character password
 *
 * Mailtrap (sandbox testing):
 *  Host: sandbox.smtp.mailtrap.io  Port: 2525
 *  Get credentials from mailtrap.io → Email Testing → Inboxes → SMTP Settings
 */

define('MAIL_HOST',       'smtp.gmail.com');       // or sandbox.smtp.mailtrap.io
define('MAIL_PORT',       587);                    // or 2525 for Mailtrap
define('MAIL_USERNAME',   'your@gmail.com');
define('MAIL_PASSWORD',   'xxxx xxxx xxxx xxxx'); // Gmail app password
define('MAIL_FROM_EMAIL', 'your@gmail.com');
define('MAIL_FROM_NAME',  'SeedCycle');
