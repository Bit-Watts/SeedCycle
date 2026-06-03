# 🚀 SeedCycle - Quick Start Guide

Get SeedCycle up and running in 5 minutes!

## Prerequisites

- ✅ XAMPP/WAMP/MAMP installed
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher

## Step-by-Step Setup

### 1. Database Setup (2 minutes)

1. Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
2. Click **"New"** to create a database
3. Name it: `seed cycle`
4. Click **"Import"** tab
5. Choose file: `database-setup.sql`
6. Click **"Go"**

✅ Database is ready!

### 2. Configure Database Connection (1 minute)

Edit `config/Database.php`:

```php
<?php
$host     = 'localhost';
$db_name  = 'seed cycle';
$username = 'root';
$password = '';  // Your MySQL password (usually empty for XAMPP)

$conn = mysqli_connect($host, $username, $password, $db_name);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_errno());
}
```

### 3. Configure Weather API (Optional - 1 minute)

1. Get free API key from [OpenWeatherMap](https://openweathermap.org/api)
2. Edit `config/Weather.php`:

```php
<?php
define('WEATHER_API_KEY', 'your_api_key_here');
define('WEATHER_API_URL', 'https://api.openweathermap.org/data/2.5/weather');
```

### 4. Install WebSocket Dependencies (2 minutes)

For real-time messaging features:

1. Open terminal in project root
2. Run: `composer install`
3. Import WebSocket tables:
   - Open phpMyAdmin
   - Select `seed cycle` database
   - Import `database-websocket-tables.sql`

### 5. Start WebSocket Server (Optional - 30 seconds)

For real-time messaging (keep terminal open):

```bash
php websocket-server.php
```

You should see: `✓ WebSocket server is running!`

**Note:** Real-time messaging requires this server to be running. See [WEBSOCKET_INSTALLATION.md](WEBSOCKET_INSTALLATION.md) for details.

### 6. Set File Permissions (30 seconds)

Make sure the uploads folder is writable:

**Windows (XAMPP):**
- Right-click `public/assets/uploads` → Properties → Security
- Give "Full Control" to "Users"

**Mac/Linux:**
```bash
chmod -R 755 public/assets/uploads
```

### 7. Access the Application (30 seconds)

Open your browser and go to:
```
http://localhost/SeedCycle/public/
```

## 🎉 You're Done!

### Default Login Credentials

**Admin Account:**
- Email: `admin@seedcycle.com`
- Password: `admin123`

**Test User Account:**
- Create your own by clicking "Sign Up"

## 🧪 Test the Features

### As a Buyer:
1. Browse the marketplace
2. Add items to cart
3. Checkout with delivery details
4. Track your order

### As a Seller:
1. Go to "Sell Seeds"
2. List a new seed
3. Wait for admin approval
4. Manage orders in "Seller Orders"

### As Admin:
1. Login with admin credentials
2. Go to `/public/admin/`
3. Approve listings
4. Manage users and orders

## 🔧 Troubleshooting

### Database Connection Error
- Check MySQL is running
- Verify database name is `seed cycle` (with space)
- Check username/password in `config/Database.php`

### Images Not Uploading
- Check folder permissions on `public/assets/uploads/`
- Verify PHP `upload_max_filesize` in `php.ini`

### Weather Not Working
- Verify API key in `config/Weather.php`
- Check internet connection
- API key may take a few minutes to activate

### Page Not Found (404)
- Make sure you're accessing via `public/` folder
- Check `.htaccess` file exists in `public/`
- Enable `mod_rewrite` in Apache

## 📁 Important Files

| File | Purpose |
|------|---------|
| `config/Database.php` | Database connection |
| `config/Weather.php` | Weather API settings |
| `database-setup.sql` | Database schema |
| `public/index.php` | Application entry point |
| `README.md` | Full documentation |

## 🎯 Next Steps

1. **Customize**: Update branding, colors, and content
2. **Add Seeds**: Populate inventory with your seeds
3. **Test**: Try all features thoroughly
4. **Deploy**: Move to production server when ready

## 📞 Need Help?

- 📖 Read the full [README.md](README.md)
- 📋 Check [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)
- 🐛 Report issues to your development team

---

**Happy Planting! 🌱**
