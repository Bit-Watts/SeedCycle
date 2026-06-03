<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — SeedCycle</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="assets/css/chat.css">
</head>
<body>
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<div class="chat-container" data-user-id="<?= (int)$user['id'] ?>">

    <!-- ── SIDEBAR ── -->
    <div class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">
            <h2><i class="fas fa-comments"></i> Messages</h2>
        </div>

        <div class="conversations-list" id="conversationsList">
            <?php if (empty($conversations)): ?>
                <div class="no-conversations">
                    <i class="fas fa-inbox"></i>
                    <p>No conversations yet</p>
                    <small>Visit a seller's profile and click "Chat Seller" to start.</small>
                </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv):
                    $myId = (int)$user['id'];
                    $isBuyer = ($conv['buyer_id'] == $myId);

                    // Other person's info
                    $otherName   = $isBuyer
                        ? $conv['seller_first_name'] . ' ' . $conv['seller_last_name']
                        : $conv['buyer_first_name']  . ' ' . $conv['buyer_last_name'];
                    $otherAvatar = $isBuyer
                        ? ($conv['seller_avatar'] ?? '')
                        : ($conv['buyer_avatar']  ?? '');

                    // Conversation label
                    $isDirect = (($conv['conversation_type'] ?? 'order') === 'direct');
                    $label    = $isDirect ? 'Direct' : 'Order #' . $conv['order_id'];

                    // Last message preview
                    $preview = $conv['last_message'] ?? '';
                    if (mb_strlen($preview) > 40) {
                        $preview = mb_substr($preview, 0, 40) . '…';
                    }
                    if (!$preview) {
                        $preview = $isDirect ? 'Say hello 👋' : 'Order conversation';
                    }
                ?>
                <div class="conversation-item <?= ($openConvId === (int)$conv['id']) ? 'active' : '' ?>"
                     data-conversation-id="<?= (int)$conv['id'] ?>"
                     onclick="loadConversation(<?= (int)$conv['id'] ?>)">

                    <div class="conversation-avatar">
                        <?php if (!empty($otherAvatar)): ?>
                            <img src="<?= htmlspecialchars($otherAvatar) ?>" alt="<?= htmlspecialchars($otherName) ?>">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>

                    <div class="conversation-info">
                        <div class="conversation-name">
                            <?= htmlspecialchars($otherName) ?>
                            <span class="conversation-role"><?= htmlspecialchars($label) ?></span>
                        </div>
                        <div class="conversation-preview"><?= htmlspecialchars($preview) ?></div>
                    </div>

                    <?php if ((int)$conv['unread_count'] > 0): ?>
                        <div class="conversation-badge"><?= (int)$conv['unread_count'] ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── MAIN CHAT AREA ── -->
    <div class="chat-main" id="chatMain">

        <!-- Empty state -->
        <div class="chat-empty" id="chatEmpty" <?= $openConvId ? 'style="display:none"' : '' ?>>
            <i class="fas fa-comments"></i>
            <h3>Select a conversation</h3>
            <p>Choose from the list or visit a seller's profile to start chatting.</p>
        </div>

        <!-- Active chat -->
        <div class="chat-active" id="chatActive" <?= $openConvId ? '' : 'style="display:none"' ?>>

            <!-- Header -->
            <div class="chat-header">
                <div class="chat-header-info">
                    <button class="chat-back-btn" onclick="showSidebar()" title="Back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <div class="chat-avatar" id="chatHeaderAvatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div>
                        <h3 id="chatUserName">—</h3>
                        <p id="chatSubtitle">Direct message</p>
                    </div>
                </div>
                <div class="chat-header-actions" id="chatHeaderActions">
                    <!-- View Order button injected here for order chats -->
                </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chatMessages"></div>

            <!-- Typing indicator -->
            <div class="typing-indicator" id="typingIndicator" style="display:none;">
                <span></span><span></span><span></span>
                <span class="typing-text">is typing…</span>
            </div>

            <!-- Input -->
            <div class="chat-input">
                <textarea id="messageInput"
                          placeholder="Type a message…"
                          rows="1"
                          maxlength="2000"></textarea>
                <button class="btn-send" id="sendButton" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/websocket-client.js"></script>
<script>
/* ── STATE ── */
const MY_ID = <?= (int)$user['id'] ?>;
let currentConvId  = null;
let currentConvData = null;
let typingTimeout  = null;
let pollInterval   = null;

/* ── HELPERS ── */
function escapeHtml(text) {
    const d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
}

function formatTime(dateStr) {
    return new Date(dateStr).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function avatarHtml(src, name) {
    if (src) return `<img src="${escapeHtml(src)}" alt="${escapeHtml(name)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
    return `<i class="fas fa-user"></i>`;
}

/* ── MOBILE PANEL SWITCHING ── */
function showChatPanel() {
  if (window.innerWidth <= 768) {
    document.getElementById('chatSidebar').classList.add('hidden');
    document.getElementById('chatActive').classList.add('visible');
    document.getElementById('chatMain').style.display = 'flex';
    document.getElementById('chatMain').classList.add('visible');
  }
}

function showSidebar() {
  if (window.innerWidth <= 768) {
    document.getElementById('chatSidebar').classList.remove('hidden');
    document.getElementById('chatActive').classList.remove('visible');
    const main = document.getElementById('chatMain');
    if (main) { main.classList.remove('visible'); main.style.display = ''; }
  }
}

/* ── LOAD CONVERSATION ── */
function loadConversation(convId) {
    currentConvId = convId;
    lastMessageId = 0; // reset before loading new conversation

    document.getElementById('chatEmpty').style.display  = 'none';
    document.getElementById('chatActive').style.display = 'flex';

    // Highlight sidebar item
    document.querySelectorAll('.conversation-item').forEach(el => el.classList.remove('active'));
    const item = document.querySelector(`[data-conversation-id="${convId}"]`);
    if (item) item.classList.add('active');

    // Clear unread badge on sidebar item
    if (item) {
        const badge = item.querySelector('.conversation-badge');
        if (badge) badge.remove();
    }

    fetch(`chat-load.php?conversation_id=${convId}`)
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            currentConvData = data.conversation;
            renderHeader(data.conversation);
            renderMessages(data.messages);
            startPolling(convId);
            showChatPanel(); // switch to chat panel on mobile
        })
        .catch(err => console.error('Load error:', err));
}

/* ── RENDER HEADER ── */
function renderHeader(conv) {
    const isBuyer    = (conv.buyer_id == MY_ID);
    const otherName  = isBuyer
        ? conv.seller_first_name + ' ' + conv.seller_last_name
        : conv.buyer_first_name  + ' ' + conv.buyer_last_name;
    const otherAvatar = isBuyer ? (conv.seller_avatar || '') : (conv.buyer_avatar || '');
    const isDirect   = (conv.conversation_type === 'direct');

    document.getElementById('chatUserName').textContent = otherName;
    document.getElementById('chatSubtitle').textContent = isDirect
        ? 'Direct message'
        : `Order #${conv.order_id}`;

    document.getElementById('chatHeaderAvatar').innerHTML = avatarHtml(otherAvatar, otherName);

    const actions = document.getElementById('chatHeaderActions');
    if (!isDirect && conv.order_id) {
        actions.innerHTML = `
            <a href="order-tracking.php?id=${conv.order_id}" class="btn-view-order">
                <i class="fas fa-box"></i> View Order
            </a>`;
    } else {
        actions.innerHTML = '';
    }
}

/* ── RENDER MESSAGES ── */
function renderMessages(messages) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = '';

    messages.forEach(msg => appendMessage(msg, false));

    // Track highest message ID so polling only fetches new ones
    if (messages.length > 0) {
        lastMessageId = Math.max(...messages.map(m => parseInt(m.id) || 0));
    }

    container.scrollTop = container.scrollHeight;
}

function appendMessage(msg, scroll = true) {
    const container = document.getElementById('chatMessages');
    const isOwn     = (msg.sender_id == MY_ID);
    const time      = formatTime(msg.created_at);

    // Track highest seen ID
    if (msg.id && parseInt(msg.id) > lastMessageId) {
        lastMessageId = parseInt(msg.id);
    }

    const div = document.createElement('div');
    div.className = `chat-message ${isOwn ? 'own' : 'other'} ${msg.message_type || 'text'}`;

    if (msg.message_type === 'order_update' || msg.message_type === 'system') {
        div.innerHTML = `
            <div class="system-message">
                <i class="fas fa-info-circle"></i>
                ${escapeHtml(msg.message)}
                <span class="message-time">${time}</span>
            </div>`;
    } else {
        div.innerHTML = `
            <div class="message-bubble">
                ${!isOwn ? `<div class="message-sender">${escapeHtml(msg.first_name || '')}</div>` : ''}
                <div class="message-text">${escapeHtml(msg.message)}</div>
                <div class="message-time">${time}</div>
            </div>`;
    }

    container.appendChild(div);
    if (scroll) container.scrollTop = container.scrollHeight;
}

/* ── SEND MESSAGE ── */
function sendMessage() {
    if (!currentConvId) return;
    const input   = document.getElementById('messageInput');
    const message = input.value.trim();
    if (!message) return;

    // Try WebSocket first
    if (window.wsClient && window.wsClient.sendChatMessage(currentConvId, message)) {
        input.value = '';
        input.style.height = 'auto';
        return;
    }

    // HTTP fallback
    const fd = new FormData();
    fd.append('conversation_id', currentConvId);
    fd.append('message', message);

    // Optimistically show the message immediately
    const tempMsg = {
        id:           null, // no ID yet — will be updated from server response
        sender_id:    MY_ID,
        message:      message,
        message_type: 'text',
        first_name:   '<?= addslashes($user['first_name']) ?>',
        created_at:   new Date().toISOString(),
    };
    appendMessage(tempMsg);
    input.value = '';
    input.style.height = 'auto';
    updateSidebarPreview(currentConvId, message);

    fetch('chat-send.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.message_id) {
                // Update lastMessageId so polling won't re-fetch this message
                const serverMsgId = parseInt(data.message_id);
                if (serverMsgId > lastMessageId) {
                    lastMessageId = serverMsgId;
                }
            }
        })
        .catch(err => console.error('Send error:', err));
}

/* ── POLLING FALLBACK (when WebSocket is offline) ── */
let lastMessageId = 0;

function startPolling(convId) {
    stopPolling();
    lastMessageId = 0; // reset for new conversation — renderMessages will set it

    pollInterval = setInterval(() => {
        if (currentConvId !== convId) { stopPolling(); return; }
        fetch(`chat-load.php?conversation_id=${convId}&since=${lastMessageId}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.messages || data.messages.length === 0) return;
                data.messages.forEach(msg => {
                    const msgId = parseInt(msg.id) || 0;
                    if (msgId <= lastMessageId) return; // already seen

                    // Always advance lastMessageId for ALL messages (own or not)
                    if (msgId > lastMessageId) lastMessageId = msgId;

                    // Only append messages from the other person — own messages
                    // were already optimistically shown on send
                    if (msg.sender_id != MY_ID) {
                        appendMessage(msg);
                        updateSidebarPreview(convId, msg.message);
                    }
                });
            })
            .catch(() => {});
    }, 4000);
}

function stopPolling() {
    if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
}

/* ── UPDATE SIDEBAR PREVIEW ── */
function updateSidebarPreview(convId, message) {
    const item = document.querySelector(`[data-conversation-id="${convId}"] .conversation-preview`);
    if (item) {
        item.textContent = message.length > 40 ? message.substring(0, 40) + '…' : message;
    }
}

/* ── KEYBOARD & RESIZE ── */
document.getElementById('messageInput').addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
});

document.getElementById('messageInput').addEventListener('input', e => {
    e.target.style.height = 'auto';
    e.target.style.height = Math.min(e.target.scrollHeight, 120) + 'px';

    if (window.wsClient && currentConvId) {
        window.wsClient.sendTyping(currentConvId, true);
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => window.wsClient.sendTyping(currentConvId, false), 2000);
    }
});

/* ── WEBSOCKET HANDLERS ── */
if (window.wsClient) {
    window.wsClient.on('chat_message', data => {
        if (data.conversation_id == currentConvId) {
            appendMessage({
                sender_id:    data.sender_id,
                message:      data.message,
                message_type: 'text',
                first_name:   (data.sender_name || '').split(' ')[0],
                created_at:   new Date().toISOString(),
            });
            updateSidebarPreview(currentConvId, data.message);
            window.wsClient.markAsRead(currentConvId);
        } else {
            // Increment unread badge on sidebar
            const item = document.querySelector(`[data-conversation-id="${data.conversation_id}"]`);
            if (item) {
                let badge = item.querySelector('.conversation-badge');
                if (!badge) {
                    badge = document.createElement('div');
                    badge.className = 'conversation-badge';
                    badge.textContent = '1';
                    item.appendChild(badge);
                } else {
                    badge.textContent = parseInt(badge.textContent || 0) + 1;
                }
            }
        }
    });

    window.wsClient.on('typing', data => {
        if (data.conversation_id == currentConvId) {
            const ind = document.getElementById('typingIndicator');
            if (data.is_typing) {
                ind.querySelector('.typing-text').textContent = `${data.user_name} is typing…`;
                ind.style.display = 'flex';
            } else {
                ind.style.display = 'none';
            }
        }
    });
}

/* ── AUTO-OPEN CONVERSATION ── */
<?php if ($openConvId > 0): ?>
document.addEventListener('DOMContentLoaded', () => loadConversation(<?= $openConvId ?>));
<?php endif; ?>
</script>
</body>
</html>
