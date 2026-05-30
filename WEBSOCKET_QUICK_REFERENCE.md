# WebSocket Quick Reference Card

## 🚀 Quick Start

### Start Server
```bash
php websocket-server.php
```

### Install Dependencies
```bash
composer install
```

### Import Database
```sql
mysql -u root -p "seed cycle" < database-websocket-tables.sql
```

## 📡 Server Connection

### URL
```
ws://localhost:8080
```

### Change Port
Edit `websocket-server.php` and `websocket-client.js`:
```php
// websocket-server.php
$server = IoServer::factory(..., 8080);  // Change port here
```
```javascript
// websocket-client.js
this.ws = new WebSocket('ws://localhost:8080');  // Change port here
```

## 💬 Message Types

### Authentication
```javascript
{
  type: 'auth',
  user_id: 123
}
```

### Chat Message
```javascript
{
  type: 'chat_message',
  conversation_id: 1,
  sender_id: 123,
  message: 'Hello!'
}
```

### Order Update
```javascript
{
  type: 'order_update',
  order_id: 456,
  status: 'shipped',
  user_id: 123,
  message: 'Your order has been shipped'
}
```

### Typing Indicator
```javascript
{
  type: 'typing',
  conversation_id: 1,
  user_id: 123,
  is_typing: true
}
```

### Mark as Read
```javascript
{
  type: 'mark_read',
  conversation_id: 1,
  user_id: 123
}
```

### Ping (Keep-Alive)
```javascript
{
  type: 'ping'
}
```

## 🔧 PHP Usage

### Send Notification
```php
use App\WebSocket\WebSocketNotifier;

$wsNotifier = new WebSocketNotifier($conn);

// Shipment created
$wsNotifier->notifyShipmentCreated($orderId, 'LBC', 'SC123456');

// Shipment updated
$wsNotifier->notifyShipmentUpdate($orderId, 'in_transit');

// Custom notification
$wsNotifier->notifyOrderUpdate($orderId, 'processing', 'Custom message');
```

### Get Conversations
```php
use App\WebSocket\ChatService;

$chatService = new ChatService($conn);

// Get user conversations
$conversations = $chatService->getUserConversations($userId);

// Get messages
$messages = $chatService->getMessages($conversationId);

// Get unread count
$unreadCount = $chatService->getUnreadCount($userId);
```

### Save Message
```php
$messageId = $chatService->saveMessage(
    $conversationId,
    $senderId,
    'Message text',
    'text'  // or 'order_update', 'system'
);
```

### Create Notification
```php
$notificationId = $chatService->createNotification(
    $orderId,
    $userId,
    'order_update',
    'Order Update',
    'Your order has been shipped'
);
```

## 🌐 JavaScript Usage

### Initialize Client
```javascript
// Auto-initializes if data-user-id is present
// Manual initialization:
initWebSocket(userId);
```

### Access Client
```javascript
// Global instance
window.wsClient
```

### Send Message
```javascript
window.wsClient.sendChatMessage(conversationId, 'Hello!');
```

### Send Typing
```javascript
window.wsClient.sendTyping(conversationId, true);
```

### Mark as Read
```javascript
window.wsClient.markAsRead(conversationId);
```

### Listen for Messages
```javascript
window.wsClient.on('chat_message', (data) => {
    console.log('New message:', data);
    // data.message_id
    // data.conversation_id
    // data.sender_id
    // data.sender_name
    // data.message
    // data.timestamp
});
```

### Listen for Order Updates
```javascript
window.wsClient.on('order_update', (data) => {
    console.log('Order update:', data);
    // data.order_id
    // data.status
    // data.message
    // data.timestamp
});
```

### Listen for Typing
```javascript
window.wsClient.on('typing', (data) => {
    if (data.is_typing) {
        showTypingIndicator(data.user_name);
    } else {
        hideTypingIndicator();
    }
});
```

### Connection Status
```javascript
window.wsClient.onConnection((isConnected) => {
    if (isConnected) {
        console.log('Connected!');
    } else {
        console.log('Disconnected!');
    }
});
```

## 🗄️ Database Queries

### Get Conversations
```sql
SELECT * FROM chat_conversations 
WHERE buyer_id = ? OR seller_id = ?
ORDER BY last_message_at DESC;
```

### Get Messages
```sql
SELECT * FROM chat_messages 
WHERE conversation_id = ?
ORDER BY created_at ASC;
```

### Get Unread Count
```sql
SELECT COUNT(*) FROM chat_messages cm
JOIN chat_conversations cc ON cc.id = cm.conversation_id
WHERE (cc.buyer_id = ? OR cc.seller_id = ?)
AND cm.sender_id != ?
AND cm.is_read = 0;
```

### Get Notifications
```sql
SELECT * FROM order_notifications
WHERE user_id = ?
ORDER BY created_at DESC
LIMIT 20;
```

### Mark as Read
```sql
UPDATE chat_messages 
SET is_read = 1 
WHERE conversation_id = ? 
AND sender_id != ?;
```

## 🐛 Debugging

### Check Server Status
```bash
# Server should show:
✓ WebSocket server is running!
  Listening on: ws://localhost:8080
```

### Check Browser Connection
```javascript
// In browser console:
console.log(window.wsClient);
// Should show WebSocketClient instance

// Check connection state:
console.log(window.wsClient.ws.readyState);
// 0 = CONNECTING
// 1 = OPEN
// 2 = CLOSING
// 3 = CLOSED
```

### Common Errors

**"Connection refused"**
- Server not running
- Wrong port
- Firewall blocking

**"Authentication failed"**
- Invalid user_id
- User not logged in

**"Message not sending"**
- Not connected
- Invalid conversation_id
- Permission denied

## 📊 Status Codes

### WebSocket ReadyState
- `0` - CONNECTING
- `1` - OPEN
- `2` - CLOSING
- `3` - CLOSED

### Shipment Statuses
- `pending` - Awaiting shipment
- `packed` - Packed and ready
- `shipped` - Shipped
- `in_transit` - In transit
- `out_for_delivery` - Out for delivery
- `delivered` - Delivered

### Message Types
- `text` - Regular chat message
- `order_update` - Order status update
- `system` - System message

## 🔐 Security

### Authentication
```php
// Server validates user on connect
if (!isset($data['user_id'])) {
    return;  // Reject
}
```

### Authorization
```php
// Check conversation access
if ($conversation['buyer_id'] != $userId && 
    $conversation['seller_id'] != $userId) {
    return;  // Access denied
}
```

### Input Sanitization
```php
// Always escape output
echo htmlspecialchars($message);
```

## 📁 File Locations

### Backend
```
app/WebSocket/
├── ChatServer.php
├── ChatService.php
└── WebSocketNotifier.php
```

### Frontend
```
public/assets/
├── js/websocket-client.js
└── css/chat.css
```

### Views
```
app/Views/
├── chat.php
└── notifications.php
```

### Public Pages
```
public/
├── chat.php
├── chat-load.php
└── notifications.php
```

## 🎯 Quick Commands

### Start Server
```bash
php websocket-server.php
```

### Stop Server
```
Ctrl+C
```

### Check Port Usage
```bash
# Windows
netstat -ano | findstr :8080

# Linux/Mac
lsof -i :8080
```

### Test Connection
```bash
# Using wscat (npm install -g wscat)
wscat -c ws://localhost:8080
```

## 📞 Support

### Documentation
- `WEBSOCKET_INSTALLATION.md` - Setup
- `WEBSOCKET_FEATURES.md` - Features
- `TESTING_GUIDE.md` - Testing

### Logs
- Server: Terminal output
- Client: Browser console (F12)
- Database: MySQL error log

---

**Quick Reference v1.0**  
**Last Updated: 2026-05-27**
