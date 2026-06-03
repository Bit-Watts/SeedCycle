/**
 * WebSocket Client for Real-Time Messaging
 * Handles connection, authentication, and message routing
 */

class WebSocketClient {
    constructor(userId) {
        this.userId = userId;
        this.ws = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000;
        this.isConnecting = false;
        this.messageHandlers = {};
        this.connectionHandlers = [];
        
        this.connect();
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        if (this.isConnecting || (this.ws && this.ws.readyState === WebSocket.OPEN)) {
            return;
        }

        this.isConnecting = true;
        console.log('Connecting to WebSocket server...');

        try {
            this.ws = new WebSocket('ws://localhost:8080');

            this.ws.onopen = () => {
                console.log('✓ WebSocket connected');
                this.isConnecting = false;
                this.reconnectAttempts = 0;
                
                // Authenticate
                this.send('auth', { user_id: this.userId });
                
                // Start ping interval
                this.startPingInterval();
                
                // Notify connection handlers
                this.connectionHandlers.forEach(handler => handler(true));
            };

            this.ws.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    this.handleMessage(data);
                } catch (e) {
                    console.error('Failed to parse message:', e);
                }
            };

            this.ws.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.isConnecting = false;
            };

            this.ws.onclose = () => {
                console.log('WebSocket disconnected');
                this.isConnecting = false;
                this.stopPingInterval();
                
                // Notify connection handlers
                this.connectionHandlers.forEach(handler => handler(false));
                
                // Attempt reconnection
                if (this.reconnectAttempts < this.maxReconnectAttempts) {
                    this.reconnectAttempts++;
                    console.log(`Reconnecting... (attempt ${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
                    setTimeout(() => this.connect(), this.reconnectDelay);
                } else {
                    console.error('Max reconnection attempts reached');
                    this.showConnectionError();
                }
            };
        } catch (e) {
            console.error('Failed to create WebSocket:', e);
            this.isConnecting = false;
        }
    }

    /**
     * Send message to server
     */
    send(type, data = {}) {
        if (!this.ws || this.ws.readyState !== WebSocket.OPEN) {
            console.warn('WebSocket not connected, cannot send message');
            return false;
        }

        try {
            this.ws.send(JSON.stringify({ type, ...data }));
            return true;
        } catch (e) {
            console.error('Failed to send message:', e);
            return false;
        }
    }

    /**
     * Handle incoming messages
     */
    handleMessage(data) {
        const { type } = data;
        
        // Call registered handlers for this message type
        if (this.messageHandlers[type]) {
            this.messageHandlers[type].forEach(handler => handler(data));
        }
        
        // Call global handlers
        if (this.messageHandlers['*']) {
            this.messageHandlers['*'].forEach(handler => handler(data));
        }
    }

    /**
     * Register message handler
     */
    on(type, handler) {
        if (!this.messageHandlers[type]) {
            this.messageHandlers[type] = [];
        }
        this.messageHandlers[type].push(handler);
    }

    /**
     * Register connection status handler
     */
    onConnection(handler) {
        this.connectionHandlers.push(handler);
    }

    /**
     * Send chat message
     */
    sendChatMessage(conversationId, message) {
        return this.send('chat_message', {
            conversation_id: conversationId,
            sender_id: this.userId,
            message: message
        });
    }

    /**
     * Send typing indicator
     */
    sendTyping(conversationId, isTyping = true) {
        return this.send('typing', {
            conversation_id: conversationId,
            user_id: this.userId,
            is_typing: isTyping
        });
    }

    /**
     * Mark messages as read
     */
    markAsRead(conversationId) {
        return this.send('mark_read', {
            conversation_id: conversationId,
            user_id: this.userId
        });
    }

    /**
     * Send order update notification
     */
    sendOrderUpdate(orderId, status, message) {
        return this.send('order_update', {
            order_id: orderId,
            status: status,
            user_id: this.userId,
            message: message
        });
    }

    /**
     * Start ping interval to keep connection alive
     */
    startPingInterval() {
        this.stopPingInterval();
        this.pingInterval = setInterval(() => {
            this.send('ping');
        }, 30000); // Ping every 30 seconds
    }

    /**
     * Stop ping interval
     */
    stopPingInterval() {
        if (this.pingInterval) {
            clearInterval(this.pingInterval);
            this.pingInterval = null;
        }
    }

    /**
     * Show connection error to user
     */
    showConnectionError() {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'websocket-error-banner';
        errorDiv.innerHTML = `
            <div class="alert alert-warning" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                <strong>Connection Lost</strong><br>
                Real-time updates are unavailable. Please refresh the page.
                <button onclick="location.reload()" class="btn btn-sm btn-primary ml-2">Refresh</button>
            </div>
        `;
        document.body.appendChild(errorDiv);
    }

    /**
     * Close connection
     */
    close() {
        this.stopPingInterval();
        if (this.ws) {
            this.ws.close();
        }
    }
}

// ============================================================================
// NOTIFICATION BADGE UPDATER
// ============================================================================

class NotificationBadge {
    constructor(wsClient) {
        this.wsClient = wsClient;
        this.badgeElement = document.querySelector('.notification-badge');
        
        // Listen for unread count updates
        this.wsClient.on('unread_count', (data) => {
            this.updateBadge(data.count);
        });
        
        // Listen for new messages
        this.wsClient.on('chat_message', () => {
            this.incrementBadge();
        });
        
        // Listen for order updates
        this.wsClient.on('order_update', () => {
            this.incrementBadge();
        });
    }

    updateBadge(count) {
        if (!this.badgeElement) return;
        
        if (count > 0) {
            this.badgeElement.textContent = count > 99 ? '99+' : count;
            this.badgeElement.style.display = 'inline-block';
        } else {
            this.badgeElement.style.display = 'none';
        }
    }

    incrementBadge() {
        if (!this.badgeElement) return;
        
        const current = parseInt(this.badgeElement.textContent) || 0;
        this.updateBadge(current + 1);
    }
}

// ============================================================================
// GLOBAL INITIALIZATION
// ============================================================================

// Initialize WebSocket client when user is logged in
let wsClient = null;
let notificationBadge = null;

function initWebSocket(userId) {
    if (!userId) {
        console.warn('Cannot initialize WebSocket: no user ID provided');
        return;
    }
    
    wsClient = new WebSocketClient(userId);
    notificationBadge = new NotificationBadge(wsClient);
    
    // Make available globally
    window.wsClient = wsClient;
    window.notificationBadge = notificationBadge;
    
    console.log('WebSocket client initialized for user:', userId);
}

// Auto-initialize if user ID is available
document.addEventListener('DOMContentLoaded', () => {
    const userIdElement = document.querySelector('[data-user-id]');
    if (userIdElement) {
        const userId = parseInt(userIdElement.dataset.userId);
        if (userId) {
            initWebSocket(userId);
        }
    }
});
