<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - SeedCycle</title>
    <link rel="stylesheet" href="assets/css/base.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notifications-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .notifications-header {
            margin-bottom: 30px;
        }

        .notifications-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #48bb78;
            text-decoration: none;
            padding: 6px 14px;
            border: 1.5px solid #48bb78;
            border-radius: 8px;
            margin-bottom: 16px;
            transition: all 0.2s;
        }
        .notifications-back-btn:hover {
            background: #48bb78;
            color: #fff;
        }

        .notifications-header h1 {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .notifications-header p {
            color: #718096;
        }

        .notification-item {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: all 0.2s;
        }

        .notification-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .notification-item.unread {
            background: #f0fff4;
            border-color: #48bb78;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.5rem;
        }

        .notification-icon.order_update {
            background: #e6fffa;
            color: #38a169;
        }

        .notification-icon.shipment {
            background: #ebf8ff;
            color: #3182ce;
        }

        .notification-icon.delivered {
            background: #fef5e7;
            color: #d69e2e;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }

        .notification-message {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 8px;
        }

        .notification-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.85rem;
            color: #a0aec0;
        }

        .notification-time {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-view-order {
            padding: 6px 16px;
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: #fff;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-view-order:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(72, 187, 120, 0.3);
        }

        .btn-mark-read {
            padding: 6px 16px;
            background: #edf2f7;
            color: #4a5568;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-mark-read:hover {
            background: #e2e8f0;
        }

        .no-notifications {
            text-align: center;
            padding: 80px 20px;
            color: #a0aec0;
        }

        .no-notifications i {
            font-size: 5rem;
            color: #e2e8f0;
            margin-bottom: 20px;
        }

        .no-notifications h3 {
            color: #4a5568;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="notifications-container" data-user-id="<?= htmlspecialchars($user['id']) ?>">
        <div class="notifications-header">
            <a href="<?= htmlspecialchars($backUrl) ?>" class="notifications-back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <h1><i class="fas fa-bell"></i> Notifications</h1>
            <p>Stay updated with your order status and messages</p>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="no-notifications">
                <i class="fas fa-bell-slash"></i>
                <h3>No notifications yet</h3>
                <p>You'll see order updates and messages here</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <?php
                    $isUnread = !$notification['is_read'];
                    $iconClass = 'order_update';
                    $icon = 'fa-box';
                    
                    if ($notification['notification_type'] === 'new_order') {
                        $iconClass = 'order_update';
                        $icon = 'fa-bag-shopping';
                    } elseif (strpos(strtolower($notification['notification_type']), 'delivered') !== false) {
                        $iconClass = 'delivered';
                        $icon = 'fa-check-circle';
                    } elseif (strpos(strtolower($notification['notification_type']), 'ship') !== false) {
                        $iconClass = 'shipment';
                        $icon = 'fa-truck';
                    }
                    
                    $timeAgo = getTimeAgo($notification['created_at']);
                ?>
                <div class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                    <div class="notification-icon <?= $iconClass ?>">
                        <i class="fas <?= $icon ?>"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title"><?= htmlspecialchars($notification['title']) ?></div>
                        <div class="notification-message"><?= htmlspecialchars($notification['message']) ?></div>
                        <div class="notification-meta">
                            <span class="notification-time">
                                <i class="fas fa-clock"></i>
                                <?= $timeAgo ?>
                            </span>
                            <span>
                                <i class="fas fa-hashtag"></i>
                                Order #<?= $notification['order_id'] ?>
                            </span>
                        </div>
                        <div class="notification-actions">
                            <?php if ($notification['notification_type'] === 'new_order'): ?>
                                <a href="seller-orders.php" class="btn-view-order">
                                    <i class="fas fa-truck-fast"></i> View Seller Orders
                                </a>
                            <?php else: ?>
                            <a href="order-tracking.php?id=<?= $notification['order_id'] ?>" class="btn-view-order">
                                <i class="fas fa-eye"></i> View Order
                            </a>
                            <?php endif; ?>
                            <?php if ($isUnread): ?>
                                <a href="notifications.php?mark_read=1&id=<?= $notification['id'] ?>&ref=<?= urlencode($backUrl) ?>" class="btn-mark-read">
                                    <i class="fas fa-check"></i> Mark as Read
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <script src="assets/js/websocket-client.js"></script>
</body>
</html>

<?php
/**
 * Helper function to get time ago string
 */
function getTimeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}
?>
