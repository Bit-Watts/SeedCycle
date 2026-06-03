# SeedCycle - Seed Marketplace & Planting Guide

A comprehensive web application for buying, selling, and managing seeds with integrated weather-based planting recommendations.

## 🌱 Features

### For Buyers
- **Marketplace**: Browse and purchase seeds from various sellers
- **Shopping Cart**: Add multiple items and checkout with COD or GCash
- **Order Tracking**: Track your orders from purchase to delivery
- **Real-Time Messaging**: Chat with sellers and receive instant order updates
- **Planting Guide**: Get weather-based recommendations for planting
- **Reviews**: Rate and review purchased seeds

### For Sellers
- **Seed Listings**: List your seeds for sale with images and details
- **Inventory Management**: Track stock levels and manage listings
- **Order Management**: View and process customer orders
- **Real-Time Notifications**: Get instant alerts for new orders
- **Shipment Tracking**: Create and update shipment information
- **Chat with Buyers**: Communicate directly with customers
- **Sales Dashboard**: Monitor your sales and revenue

### Admin Features
- **User Management**: Manage user accounts and permissions
- **Listing Approval**: Review and approve seed listings
- **Order Oversight**: Monitor all orders and shipments
- **Reports**: Generate sales and activity reports

## 📁 Project Structure

```
SeedCycle/
├── app/
│   ├── Controllers/          # Application logic
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── CartController.php
│   │   ├── OrderController.php
│   │   ├── PaymentController.php
│   │   └── ...
│   ├── Models/              # Database models
│   │   ├── User.php
│   │   ├── Order.php
│   │   ├── Seed.php
│   │   ├── Shipment.php
│   │   └── ...
│   └── Views/               # HTML templates
│       ├── admin/
│       ├── auth/
│       ├── includes/
│       └── ...
├── config/                  # Configuration files
│   ├── Database.php
│   ├── DB.php
│   └── Weather.php
├── public/                  # Public web root
│   ├── assets/
│   │   ├── css/            # Stylesheets
│   │   └── uploads/        # User uploads
│   ├── admin/              # Admin pages
│   ├── index.php           # Entry point
│   └── ...
├── .gitignore
├── composer.json
└── README.md
```

## 🚀 Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SeedCycle
   ```

2. **Configure Database**
   - Create a MySQL database named `seed cycle`
   - Import the database schema:
     ```bash
     mysql -u root -p "seed cycle" < seed_cycle.sql
     ```

3. **Configure Database Connection**
   - Edit `config/Database.php`:
     ```php
     $host     = 'localhost';
     $db_name  = 'seed cycle';
     $username = 'root';
     $password = 'your_password';
     ```

4. **Set Up Weather API** (Optional)
   - Get an API key from OpenWeatherMap
   - Edit `config/Weather.php`:
     ```php
     define('WEATHER_API_KEY', 'your_api_key_here');
     ```

5. **Install WebSocket Dependencies**
   ```bash
   composer install
   ```
   This installs the Ratchet WebSocket library for real-time messaging.

6. **Set Up WebSocket Tables**
   - Import the WebSocket database schema:
     ```bash
     mysql -u root -p "seed cycle" < database-websocket-tables.sql
     ```
   - See `WEBSOCKET_INSTALLATION.md` for detailed setup instructions

7. **Start WebSocket Server** (for real-time messaging)
   ```bash
   php websocket-server.php
   ```
   Keep this running in a separate terminal window.

8. **Configure Web Server**
   - Point document root to `public/` directory
   - Enable `.htaccess` (Apache) or configure URL rewriting

9. **Set Permissions**
   ```bash
   chmod -R 755 public/assets/uploads
   ```

10. **Access the Application**
    - Navigate to `http://localhost/SeedCycle/public/`
    - Default admin credentials (if seeded):
      - Email: admin@seedcycle.com
      - Password: admin123

## 🗄️ Database Schema

### Main Tables
- **users**: User accounts and profiles
- **inventory**: Seed inventory items
- **seed_listings**: Marketplace listings
- **orders**: Customer orders
- **order_items**: Order line items
- **shipments**: Shipment tracking
- **shipment_logs**: Shipment status history
- **reviews**: Product reviews
- **reports**: User reports
- **payment_transactions**: Payment records
- **chat_conversations**: Real-time chat conversations
- **chat_messages**: Chat messages between buyers and sellers
- **order_notifications**: Order update notifications
- **websocket_connections**: Active WebSocket connections

## 🎨 Technology Stack

- **Backend**: PHP (procedural & OOP)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Real-Time**: WebSocket (Ratchet)
- **Styling**: Custom CSS with modern design
- **Icons**: Font Awesome 6
- **Fonts**: Google Fonts (Poppins, Inter, Roboto)

## 🔐 Security Features

- Password hashing with bcrypt
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- CSRF protection (session tokens)
- Role-based access control
- Secure file upload handling

## 📱 Responsive Design

The application is fully responsive and works on:
- Desktop (1920px+)
- Laptop (1366px - 1920px)
- Tablet (768px - 1366px)
- Mobile (320px - 768px)

## 🌤️ Weather Integration

The planting guide integrates with OpenWeatherMap API to provide:
- Real-time weather data
- Temperature and humidity
- Rainfall predictions
- Planting recommendations based on weather
- Seasonal crop suggestions

## 💳 Payment Methods

- **Cash on Delivery (COD)**: Pay when you receive
- **GCash**: Online payment simulation

## 📦 Order Flow

1. Browse marketplace
2. Add items to cart
3. Checkout with delivery details
4. Choose payment method
5. Order confirmation
6. Seller creates shipment
7. Track delivery
8. Order delivered
9. Leave review

## 🛠️ Development

### Code Style
- Follow PSR-12 coding standards
- Use meaningful variable names
- Comment complex logic
- Keep functions focused and small

### Adding New Features
1. Create controller in `app/Controllers/`
2. Create model in `app/Models/`
3. Create view in `app/Views/`
4. Add route in `public/`
5. Update navigation if needed

## 📄 License

This project is proprietary software. All rights reserved.

## 👥 Contributors

- Development Team
- UI/UX Design Team
- Quality Assurance Team

## 📞 Support

For support, please contact:
- Email: support@seedcycle.com
- Phone: +63 XXX XXX XXXX

## 🔄 Version History

### v1.0.0 (Current)
- Initial release
- Core marketplace functionality
- Order management system
- Shipment tracking
- Weather-based planting guide
- Payment integration
- Admin dashboard

---

**SeedCycle** - Growing Together, Sustainably 🌱
