# WebSocket Real-Time Messaging Features

## 🎯 Overview

SeedCycle now includes a complete real-time messaging system using WebSocket technology. This enables instant communication between buyers and sellers without page refreshes.

## ✨ Features Implemented

### 1. Real-Time Chat
- **Buyer-Seller Communication**: Direct messaging for each order
- **Instant Delivery**: Messages appear immediately without refresh
- **Typing Indicators**: See when the other person is typing
- **Read Receipts**: Track when messages are read
- **Message History**: All messages saved to database

### 2. Order Notifications
- **Status Updates**: Instant notifications when order status changes
- **Shipment Tracking**: Real-time updates on shipment progress
- **Delivery Alerts**: Immediate notification when order is delivered
- **Notification Badge**: Unread count displayed in navbar

### 3. User Roles

#### Buyers Receive:
- ✅ Order status updates (pending → processing → shipped → delivered)
- ✅ Shipment creation notifications with tracking number
- ✅ Seller messages and replies
- ✅ Delivery confirmations

#### Sellers Receive:
- ✅ New order notifications
- ✅ Buyer messages and inquiries
- ✅ Order-related updates

## 📂 Files Created

### Backend (PHP)
```
app/WebSocket/
├── ChatServer.php           # WebSocket server implementation
├── ChatService.php          # Database operations for chat
└── WebSocketNotifier.php    # Helper for sending notifications

app/Views/
├── chat.php                 # Chat interface view
└── notifications.php        # Notifications list view

public/
├── chat.php                 # Chat page controller
├── chat-load.php            # AJAX endpoint for loading messages
└── notifications.php        # Notifications page controller

websocket-server.php         # Server startup script
```

### Frontend (JavaScript/CSS)
```
public/assets/
├── js/
│   └── websocket-client.js  # WebSocket client library
└── css/
    └── chat.css             # Chat interface styles
```

### Database
```
database-websocket-tables.sql  # WebSocket database schema
```

### Documentation
```
WEBSOCKET_INSTALLATION.md      # Installation guide
WEBSOCKET_FEATURES.md          # This file
```

## 🗄️ Database Tables

### chat_conversations
Stores conversations between buyers and sellers for each order.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| order_id | INT | Related order |
| buyer_id | INT | Buyer user ID |
| seller_id | INT | Seller user ID |
| status | ENUM | active/closed |
| last_message_at | TIMESTAMP | Last message time |

### chat_messages
Stores all chat messages.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| conversation_id | INT | Related conversation |
| sender_id | INT | Message sender |
| message | TEXT | Message content |
| message_type | ENUM | text/order_update/system |
| is_read | TINYINT | Read status |
| created_at | TIMESTAMP | Message time |

### order_notifications
Stores order update notifications.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| order_id | INT | Related order |
| user_id | INT | Recipient user |
| notification_type | VARCHAR | Type of notification |
| title | VARCHAR | Notification title |
| message | TEXT | Notification message |
| is_read | TINYINT | Read status |
| created_at | TIMESTAMP | Notification time |

### websocket_connections
Tracks active WebSocket connections.

| Column | Type | Description |
|--------|------|-------------|
| id | INT | Primary key |
| user_id | INT | Connected user |
| connection_id | VARCHAR | Connection identifier |
| last_ping | TIMESTAMP | Last ping time |
| created_at | TIMESTAMP | Connection time |

## 🔄 Message Flow

### Chat Message Flow
```
1. User types message in chat interface
2. JavaScript sends via WebSocket: { type: 'chat_message', ... }
3. ChatServer receives and validates
4. ChatService saves to database
5. ChatServer broadcasts to both buyer and seller
6. Recipients receive message instantly
7. Message appears in chat interface
```

### Order Update Flow
```
1. Seller updates shipment status
2. OrderController calls WebSocketNotifier
3. WebSocketNotifier saves to database
4. Message saved as 'order_update' type
5. Notification created for buyer
6. WebSocket broadcasts update
7. Buyer receives real-time notification
8. Notification badge updates
```

## 🎨 User Interface

### Chat Page (`/chat.php`)
- **Sidebar**: List of conversations with unread badges
- **Main Area**: Selected conversation with messages
- **Message Input**: Text area with send button
- **Typing Indicator**: Shows when other person is typing
- **Order Link**: Quick access to order details

### Notifications Page (`/notifications.php`)
- **List View**: All notifications with icons
- **Unread Highlighting**: Green background for unread
- **Quick Actions**: View order, mark as read
- **Time Stamps**: Relative time (e.g., "2 hours ago")

### Navbar Integration
- **Messages Icon**: Link to chat page
- **Notification Badge**: Red badge with unread count
- **Auto-Update**: Badge updates in real-time

## 🔧 Technical Details

### WebSocket Server
- **Library**: Ratchet (cboden/ratchet)
- **Port**: 8080 (configurable)
- **Protocol**: WebSocket (ws://)
- **Connection**: Persistent, bidirectional

### Client Connection
- **Auto-Connect**: Initializes on page load
- **Auto-Reconnect**: Up to 5 attempts with 3s delay
- **Ping/Pong**: Keep-alive every 30 seconds
- **Error Handling**: Graceful degradation

### Message Types
```javascript
// Authentication
{ type: 'auth', user_id: 123 }

// Chat message
{ type: 'chat_message', conversation_id: 1, sender_id: 123, message: 'Hello' }

// Order update
{ type: 'order_update', order_id: 456, status: 'shipped', message: '...' }

// Typing indicator
{ type: 'typing', conversation_id: 1, user_id: 123, is_typing: true }

// Mark as read
{ type: 'mark_read', conversation_id: 1, user_id: 123 }

// Ping (keep-alive)
{ type: 'ping' }
```

## 🔐 Security Features

### Authentication
- Users must authenticate with user_id
- Server validates user permissions
- Only conversation participants can access messages

### Authorization
- Buyers can only see their own orders
- Sellers can only see orders for their seeds
- Admins have full access (future enhancement)

### Input Validation
- All messages sanitized before display
- SQL injection prevention via prepared statements
- XSS prevention via HTML escaping
- Message length limits (1000 characters)

### Connection Security
- User ID verification on connect
- Permission checks on every action
- Automatic disconnection on auth failure

## 📊 Performance Considerations

### Scalability
- **Current**: Single server, ~1000 concurrent connections
- **Optimization**: Connection pooling, message queuing
- **Future**: Redis for pub/sub, load balancing

### Database
- Indexed columns for fast queries
- Conversation lookup by order_id
- Message pagination (50 per load)
- Automatic cleanup of old connections

### Client-Side
- Efficient DOM updates
- Message batching for bulk updates
- Lazy loading of conversations
- Debounced typing indicators

## 🚀 Usage Examples

### For Developers

#### Send Order Update Notification
```php
// In OrderController or similar
$wsNotifier = new \App\WebSocket\WebSocketNotifier($conn);
$wsNotifier->notifyShipmentCreated($orderId, 'LBC', 'SC123456789');
```

#### Send Custom Notification
```php
$wsNotifier->notifyOrderUpdate(
    $orderId, 
    'processing', 
    'Your order is being prepared for shipment'
);
```

#### Get User Conversations
```php
$chatService = new \App\WebSocket\ChatService($conn);
$conversations = $chatService->getUserConversations($userId);
```

#### Get Unread Count
```php
$unreadCount = $chatService->getUnreadCount($userId);
```

### For Frontend

#### Initialize WebSocket
```javascript
// Automatically initialized if data-user-id is present
// Manual initialization:
initWebSocket(userId);
```

#### Send Chat Message
```javascript
window.wsClient.sendChatMessage(conversationId, 'Hello!');
```

#### Listen for Messages
```javascript
window.wsClient.on('chat_message', (data) => {
    console.log('New message:', data.message);
    // Update UI
});
```

#### Send Typing Indicator
```javascript
window.wsClient.sendTyping(conversationId, true);
```

## 🐛 Common Issues & Solutions

### WebSocket Won't Connect
**Problem**: Browser console shows connection error  
**Solution**: 
1. Check WebSocket server is running
2. Verify port 8080 is not blocked
3. Check `websocket-client.js` has correct URL

### Messages Not Appearing
**Problem**: Messages sent but not received  
**Solution**:
1. Check WebSocket connection status
2. Verify database tables exist
3. Check server terminal for errors
4. Ensure user is authenticated

### Notification Badge Not Updating
**Problem**: Badge shows wrong count  
**Solution**:
1. Clear browser cache
2. Check `websocket-client.js` is loaded
3. Verify navbar has `data-user-id` attribute
4. Check browser console for errors

## 🎯 Future Enhancements

### Planned Features
- [ ] File attachments in chat
- [ ] Image sharing
- [ ] Voice messages
- [ ] Group chat for multiple sellers
- [ ] Admin monitoring dashboard
- [ ] Message search
- [ ] Emoji support
- [ ] Push notifications (mobile)

### Performance Improvements
- [ ] Redis integration for pub/sub
- [ ] Message compression
- [ ] Connection pooling
- [ ] Load balancing
- [ ] CDN for static assets

### Security Enhancements
- [ ] WSS (secure WebSocket)
- [ ] Rate limiting
- [ ] Message encryption
- [ ] Spam detection
- [ ] Profanity filter

## 📞 Support

For issues or questions:
1. Check [WEBSOCKET_INSTALLATION.md](WEBSOCKET_INSTALLATION.md)
2. Review server logs in terminal
3. Check browser console for errors
4. Verify database schema is correct

## 🎉 Success Metrics

After implementation, you should have:
- ✅ Real-time chat between buyers and sellers
- ✅ Instant order notifications
- ✅ Notification badges in navbar
- ✅ No page refresh needed
- ✅ Typing indicators
- ✅ Read receipts
- ✅ Message history
- ✅ Persistent connections

---

**Built with ❤️ for SeedCycle**
