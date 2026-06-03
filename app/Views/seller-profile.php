<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?> - Seller Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/seller-profile.css') ?>">
</head>
<body>
<?php
if ($user) {
    require __DIR__ . '/includes/navbar.php';
}
?>

<div class="sc-dashboard">
    <?php if ($user): ?>
    <?php $activePage = 'marketplace'; require __DIR__ . '/includes/sidebar.php'; ?>
    <?php endif; ?>

    <main class="sc-main">
        <!-- Breadcrumb -->
        <div class="sc-breadcrumb">
            <a href="marketplace.php"><i class="fas fa-arrow-left"></i> Back to Marketplace</a>
        </div>

        <!-- Seller Profile Header -->
        <div class="seller-profile-header">
            <div class="seller-profile-avatar">
                <?php if (!empty($seller['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($seller['profile_image']) ?>" alt="<?= htmlspecialchars($seller['first_name']) ?>">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <div class="seller-profile-info">
                <h1><?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?></h1>
                <p class="seller-profile-email"><i class="fas fa-envelope"></i> <?= htmlspecialchars($seller['email']) ?></p>
                <?php if (!empty($seller['address'])): ?>
                    <p class="seller-profile-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($seller['address']) ?></p>
                <?php endif; ?>
                <p class="seller-profile-joined"><i class="fas fa-calendar"></i> Member since <?= date('F Y', strtotime($seller['created_at'])) ?></p>
            </div>

            <!-- Chat icon on the right -->
            <div class="seller-header-chat">
                <?php if ($canChat): ?>
                    <?php
                        $chatUrl = $existingConvId
                            ? "chat.php?conv={$existingConvId}"
                            : "chat-start.php?with={$sellerId}";
                    ?>
                    <a href="<?= $chatUrl ?>" class="seller-chat-icon-btn" title="<?= $existingConvId ? 'Continue Chat' : 'Chat Seller' ?>">
                        <i class="fas fa-comments"></i>
                    </a>
                <?php elseif ($user && !$canChat): ?>
                    <!-- Viewing own profile — chat disabled -->
                    <span class="seller-chat-icon-btn seller-chat-icon-btn--disabled" title="This is your profile">
                        <i class="fas fa-comments"></i>
                    </span>
                <?php else: ?>
                    <a href="login.php" class="seller-chat-icon-btn seller-chat-icon-btn--ghost" title="Login to Chat">
                        <i class="fas fa-comments"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seller Statistics -->
        <div class="seller-stats-grid">
            <div class="seller-stat-card">
                <div class="seller-stat-icon" style="background: #E8F5E9;">
                    <i class="fas fa-seedling" style="color: #2E7D32;"></i>
                </div>
                <div class="seller-stat-info">
                    <div class="seller-stat-value"><?= (int)$sellerStats['total_listings'] ?></div>
                    <div class="seller-stat-label">Active Listings</div>
                </div>
            </div>

            <div class="seller-stat-card">
                <div class="seller-stat-icon" style="background: #FFF9C4;">
                    <i class="fas fa-shopping-bag" style="color: #F57F17;"></i>
                </div>
                <div class="seller-stat-info">
                    <div class="seller-stat-value"><?= (int)$sellerStats['total_sales'] ?></div>
                    <div class="seller-stat-label">Total Sales</div>
                </div>
            </div>

            <div class="seller-stat-card">
                <div class="seller-stat-icon" style="background: #FFF3E0;">
                    <i class="fas fa-star" style="color: #FF9800;"></i>
                </div>
                <div class="seller-stat-info">
                    <div class="seller-stat-value">
                        <?= number_format($sellerStats['avg_rating'], 1) ?>
                        <span style="font-size: 0.6em; color: #FF9800;">★</span>
                    </div>
                    <div class="seller-stat-label"><?= (int)$sellerStats['total_reviews'] ?> Reviews</div>
                </div>
            </div>
        </div>

        <!-- Seller's Listings -->
        <div class="seller-listings-section">
            <h2><i class="fas fa-store"></i> Seller's Listings</h2>
            
            <?php if (empty($sellerListings)): ?>
                <div class="no-listings">
                    <i class="fas fa-seedling"></i>
                    <p>This seller has no active listings at the moment.</p>
                </div>
            <?php else: ?>
                <div class="seller-listings-grid">
                    <?php foreach ($sellerListings as $listing): ?>
                        <a href="seed-details.php?id=<?= (int)$listing['id'] ?>" class="seller-listing-card">
                            <div class="seller-listing-image">
                                <?php if (!empty($listing['image_url'])): ?>
                                    <img src="<?= htmlspecialchars($listing['image_url']) ?>" alt="<?= htmlspecialchars($listing['name']) ?>">
                                <?php else: ?>
                                    <div class="seller-listing-placeholder">
                                        <i class="fas fa-seedling"></i>
                                    </div>
                                <?php endif; ?>
                                <?php if ($listing['stock_quantity'] <= 0): ?>
                                    <div class="seller-listing-badge out-of-stock">Out of Stock</div>
                                <?php elseif ($listing['stock_quantity'] <= 5): ?>
                                    <div class="seller-listing-badge low-stock">Low Stock</div>
                                <?php endif; ?>
                            </div>
                            <div class="seller-listing-info">
                                <h3><?= htmlspecialchars($listing['name']) ?></h3>
                                <?php if (!empty($listing['category'])): ?>
                                    <span class="seller-listing-category"><?= htmlspecialchars($listing['category']) ?></span>
                                <?php endif; ?>
                                <div class="seller-listing-meta">
                                    <?php if (!empty($listing['month_range'])): ?>
                                        <span><i class="fas fa-calendar"></i> <?= htmlspecialchars($listing['month_range']) ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-box"></i> <?= (int)$listing['stock_quantity'] ?> available</span>
                                </div>
                                <div class="seller-listing-price">₱<?= number_format($listing['price'], 2) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php if ($user): ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
<?php require __DIR__ . '/includes/logout-modal.php'; ?>
<?php endif; ?>

</body>
</html>
