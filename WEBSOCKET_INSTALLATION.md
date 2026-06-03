# WebSocket Real-Time Messaging Installation Guide

This guide will help you set up the WebSocket real-time messaging system for SeedCycle.

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL/MariaDB database
- Composer (PHP dependency manager)
- Command line access

## 🚀 Installation Steps

### Step 1: Install Composer Dependencies

Open your terminal in the project root directory and run:

```bash
composer install
```

This will install:
- `cboden/ratchet` - WebSocket server library
- `pragmarx/google2fa-qrcode` - Two-factor authentication (already installed)
- `bacon/bacon-qr-code` - QR code generation (already installed)

**Note:** If you don't have Composer installed, download it from [getcomposer.org](https://getcomposer.org/)

### Step 2: Set Up Database Tables

Run the WebSocket tables SQL script in your MySQL database:

1. Open phpMyAdmin or your MySQL client
2. Select your `seed cycle` database
3. Go to the SQL tab
4. Copy and paste the contents of `database-websocket-tables.sql`
5. Click "Go" to execute

This will create 4 new tables:
- `chat_conversations` - Stores conversations between buyers and sellers
- `chat_messages` - Stores all chat messages
- `order_notifications` - Stores order update notifications
- `websocket_connections` - Tracks active WebSocket connections

### Step 3: Start the WebSocket Server

The WebSocket server must be running for real-time messaging to work.

**Option A: Run in Terminal (Development)**

Open a new terminal window and run:

```bash
php websocket-server.php
```

You should see:
```
==============================================
  SeedCycle WebSocket Server
==============================================
Starting WebSocket server on port 8080...
Press Ctrl+C to stop the server.
==============================================

✓ WebSocket server is running!
  Listening on: ws://localhost:8080
```

**Keep this terminal window open** while using the application.

**Option B: Run as Background Process (Production)**

For production, you should run the WebSocket server as a background service:

**Windows (using NSSM):**
1. Download NSSM from [nssm.cc](https://nssm.cc/)
2. Run: `nssm install SeedCycleWebSocket`
3. Set path to PHP executable
4. Set arguments: `path\to\websocket-server.php`
5. Start service: `nssm start SeedCycleWebSocket`

**Linux (using systemd):**
Create `/etc/systemd/system/seedcycle-websocket.service`:
```ini
[Unit]
Description=SeedCycle WebSocket Server
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/SeedCycle
ExecStart=/usr/bin/php /path/to/SeedCycle/websocket-server.php
Restart=always

[Install]
WantedBy=multi-user.target
```

Then run:
```bash
sudo systemctl enable seedcycle-websocket
sudo systemctl start seedcycle-websocket
```

### Step 4: Verify Installation

1. **Check WebSocket Server Status**
   - The terminal should show "WebSocket server is running!"
   - No error messages should appear

2. **Test in Browser**
   - Log in to SeedCycle
   - Open browser console (F12)
   - You should see: `✓ WebSocket connected`
   - If you see connection errors, check that the server is running

3. **Test Messaging**
   - Place a test order
   - Go to "Messages" in the navigation
   - You should see a conversation for your order
   - As a seller, update the shipment status
   - The buyer should receive a real-time notification

## 🔧 Configuration

### Change WebSocket Port

If port 8080 is already in use, you can change it:

1. Edit `websocket-server.php`:
   ```php
   $server = IoServer::factory(
       new HttpServer(
           new WsServer(
               new ChatServer()
           )
       ),
       8080  // Change this to your desired port
   );
   ```

2. Edit `public/assets/js/websocket-client.js`:
   ```javascript
   this.ws = new WebSocket('ws://localhost:8080');  // Update port here
   ```

### Production Deployment

For production deployment:

1. **Use WSS (Secure WebSocket)**
   - Set up SSL certificate
   - Configure reverse proxy (nginx/Apache)
   - Update client to use `wss://` instead of `ws://`

2. **Configure Firewall**
   - Allow incoming connections on WebSocket port
   - Restrict access to trusted IPs if needed

3. **Monitor Server**
   - Set up logging
   - Monitor server uptime
   - Configure automatic restart on failure

## 📱 Features

### For Buyers
- ✅ Receive real-time order status updates
- ✅ Chat with sellers about orders
- ✅ Get notifications without page refresh
- ✅ See typing indicators
- ✅ Track message read status

### For Sellers
- ✅ Receive all order notifications in real-time
- ✅ Chat with buyers about orders
- ✅ Send order updates that notify buyers instantly
- ✅ See when buyers read messages

## 🐛 Troubleshooting

### WebSocket Server Won't Start

**Error: "Port 8080 is already in use"**
- Solution: Change the port (see Configuration section)
- Or stop the process using port 8080

**Error: "Composer dependencies not installed"**
- Solution: Run `composer install`

**Error: "Database connection failed"**
- Solution: Check `config/Database.php` settings
- Ensure MySQL server is running

### Browser Can't Connect

**Error: "WebSocket connection failed"**
- Check that WebSocket server is running
- Verify the port is correct in `websocket-client.js`
- Check browser console for detailed error messages

**Error: "Max reconnection attempts reached"**
- WebSocket server is not running or crashed
- Restart the WebSocket server

### Messages Not Sending

**Messages don't appear in real-time**
- Check WebSocket connection status in browser console
- Verify database tables were created correctly
- Check WebSocket server terminal for errors

**Notifications not showing**
- Clear browser cache
- Check that `websocket-client.js` is loaded
- Verify user ID is set in navbar

## 📚 Technical Details

### Architecture

```
┌─────────────┐         ┌──────────────────┐         ┌──────────────┐
│   Browser   │ ◄─────► │ WebSocket Server │ ◄─────► │   Database   │
│  (Client)   │   WS    │   (Port 8080)    │  MySQL  │ (seed cycle) │
└─────────────┘         └──────────────────┘         └──────────────┘
      │                          │
      │                          │
      └──────────► HTTP ◄────────┘
                (PHP Pages)
```

### Message Flow

1. **Order Update:**
   - Seller updates shipment status
   - `OrderController` calls `WebSocketNotifier`
   - Message saved to database
   - WebSocket server broadcasts to connected clients
   - Buyer receives real-time notification

2. **Chat Message:**
   - User types message in chat
   - JavaScript sends via WebSocket
   - Server saves to database
   - Server broadcasts to both buyer and seller
   - Message appears instantly

### Database Schema

**chat_conversations**
- Links orders to chat conversations
- Tracks buyer and seller IDs
- Stores last message timestamp

**chat_messages**
- Stores all messages (text, order updates, system)
- Tracks read status
- Links to conversation and sender

**order_notifications**
- Stores notifications for users
- Tracks read status
- Links to orders

**websocket_connections**
- Tracks active connections (for monitoring)
- Stores last ping time

## 🔐 Security Considerations

1. **Authentication**
   - Users must authenticate with user ID
   - Server verifies user permissions
   - Users can only access their own conversations

2. **Input Validation**
   - All messages are sanitized
   - SQL injection prevention via prepared statements
   - XSS prevention via HTML escaping

3. **Rate Limiting**
   - Consider implementing rate limiting for production
   - Prevent spam and abuse

## 📞 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review WebSocket server logs in terminal
3. Check browser console for JavaScript errors
4. Verify database tables exist and have correct structure

## 🎉 Success!

Once installed, you should have:
- ✅ Real-time order notifications
- ✅ Live chat between buyers and sellers
- ✅ Notification badges in navbar
- ✅ No page refresh needed for updates

Enjoy your real-time messaging system!
