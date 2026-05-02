# 🌱 SeedCycle

A PHP MVC e-commerce web application for buying and selling seeds locally.

## Features

- **Marketplace** — Browse and buy seeds with search & filter
- **Seller System** — Submit seeds for listing, manage stock, view incoming orders
- **Order Management** — Checkout, order tracking, shipment status timeline
- **Admin Panel** — Manage users, listings, orders, shipments, and reports
- **Reviews & Reports** — Star ratings on seeds, report system for seeds and reviews
- **Planting Guide** — Month-based planting calendar from inventory data

## Tech Stack

- **Backend:** PHP (MVC, no framework)
- **Database:** MySQL (via XAMPP)
- **Frontend:** HTML, CSS, vanilla JavaScript

## Setup

### 1. Requirements
- XAMPP (PHP 8.0+, MySQL 5.7+)
- Web browser

### 2. Installation

```bash
# Clone the repo into your XAMPP htdocs folder
git clone https://github.com/yourusername/SeedCycle.git C:/xampp/htdocs/SeedCycle
```

### 3. Database Setup

1. Start XAMPP (Apache + MySQL)
2. Open phpMyAdmin → create a database called `seed cycle`
3. Import `seed_cycle.sql` via the SQL tab
4. Copy `config/Database.example.php` to `config/Database.php`
5. Update credentials in `config/Database.php` if needed

### 4. Run

Visit: `http://localhost/SeedCycle/public/landing.php`

### 5. Admin Access

Run this SQL to create an admin account:

```sql
INSERT INTO users (first_name, last_name, username, email, password_hash, role, is_active)
VALUES (
  'Admin', 'Admin', 'admin', 'admin@seedcycle.com',
  '$2y$10$PCCbyDU.rDbYg4MlcNm1Ye/IKnJjXE6D/1c8FhJYxDWJu9.jom1VK',
  'admin', 1
);
```

Login with:
- **Email:** `admin@seedcycle.com`
- **Password:** `adminadmin123`

## Project Structure

```
SeedCycle/
├── app/
│   ├── Controllers/     # Business logic
│   ├── Models/          # Database queries
│   └── Views/           # HTML templates
│       ├── admin/       # Admin panel views
│       ├── auth/        # Login & signup
│       ├── includes/    # Shared partials (navbar, sidebar, footer)
│       └── seeds/       # Seed detail & sell views
├── config/
│   ├── Database.php     # DB connection (not in repo)
│   └── DB.php           # Query helper
├── public/
│   ├── assets/css/      # Stylesheets
│   └── *.php            # Entry points
└── seed_cycle.sql       # Database schema
```

## License

MIT
