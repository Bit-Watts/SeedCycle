# Direct Chat Feature — Setup Guide

## 1. Run the database migration

Open **phpMyAdmin** → select the `seed cycle` database → SQL tab, then paste and run:

```sql
-- Make order_id nullable
ALTER TABLE `chat_conversations`
  MODIFY COLUMN `order_id` INT(11) NULL DEFAULT NULL;

-- Add conversation_type column
ALTER TABLE `chat_conversations`
  ADD COLUMN IF NOT EXISTS `conversation_type`
    ENUM('order', 'direct') NOT NULL DEFAULT 'order';

-- Back-fill existing rows
UPDATE `chat_conversations`
  SET `conversation_type` = 'order'
  WHERE order_id IS NOT NULL;

-- Unique constraint (prevents duplicate direct conversations)
ALTER TABLE `chat_conversations`
  ADD UNIQUE KEY `uq_direct_chat` (`buyer_id`, `seller_id`, `conversation_type`);
```

Or run the file directly:
```
source C:/xampp/htdocs/SeedCycle/database-direct-chat.sql
```

---

## 2. Files added / changed

| File | What changed |
|---|---|
| `database-direct-chat.sql` | Migration SQL |
| `public/chat-start.php` | Creates/finds direct conversation, redirects to chat |
| `public/chat-send.php` | HTTP fallback for sending messages (no WebSocket needed) |
| `public/chat-load.php` | Updated — supports `?since=<id>` for polling, returns avatars |
| `public/chat.php` | Updated — passes `$openConvId` to view |
| `app/Views/chat.php` | Fully rewritten — handles direct + order chats, polling fallback, avatars |
| `app/Views/seller-profile.php` | Added Chat Seller button |
| `public/seller-profile.php` | Added `$canChat` + `$existingConvId` logic |
| `app/WebSocket/ChatService.php` | Updated `getMessages()`, `getConversation()`, `getUserConversations()`, `getOrCreateConversation()` |
| `public/assets/css/seller-profile.css` | Added `.seller-chat-btn` styles |
| `public/assets/css/chat.css` | Fixed avatar image sizing |

---

## 3. How it works

### Visiting a seller profile
1. User visits `seller-profile.php?id=5`
2. If logged in and not the seller → **Chat Seller** button appears
3. If a conversation already exists → button says **Continue Chat** and links directly
4. If no conversation yet → button links to `chat-start.php?with=5`

### chat-start.php flow
```
chat-start.php?with=5
  ↓
Normalise IDs (buyer = min, seller = max) to prevent duplicates
  ↓
Check: does direct conversation exist?
  ├── YES → redirect to chat.php?conv=<id>
  └── NO  → INSERT new conversation → redirect to chat.php?conv=<id>
```

### chat.php
- Sidebar shows all conversations (order + direct) with last message preview
- Clicking a conversation loads messages via `chat-load.php`
- `?conv=<id>` auto-opens that conversation on page load
- Sending uses WebSocket if available, falls back to `chat-send.php` (HTTP POST)
- Polling every 4 seconds when WebSocket is offline

### Duplicate prevention
- `buyer_id` is always `min(myId, otherId)`, `seller_id` is always `max(myId, otherId)`
- Unique key `(buyer_id, seller_id, conversation_type)` prevents duplicates at DB level
- `chat-start.php` checks before inserting

---

## 4. No WebSocket? No problem

The chat works fully without the WebSocket server:
- Messages sent via `chat-send.php` (HTTP POST)
- New messages received via polling every 4 seconds
- Start WebSocket for real-time: `php websocket-server.php`

---

## 5. Testing

1. Log in as **User A** → visit User B's seller profile → click **Chat Seller**
2. Should redirect to `chat.php?conv=<id>` with the conversation open
3. Send a message → it appears immediately
4. Log in as **User B** → go to `chat.php` → conversation appears in sidebar with preview
5. Click it → messages load, reply works
6. Visit User B's profile again as User A → button now says **Continue Chat**
