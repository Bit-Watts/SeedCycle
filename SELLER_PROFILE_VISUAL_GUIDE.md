# Seller Profile Feature - Visual Guide

## 1. Marketplace View (Updated)

### Before:
```
┌─────────────────────────┐
│  🌱 Tomato Seeds        │
│  Vegetable              │
│  ★★★★★ (12)            │
│  Jan – Mar              │
│  60 days to grow        │
│  ₱50.00 / pack          │
│  [View] [Add to Cart]   │
└─────────────────────────┘
```

### After (NEW):
```
┌─────────────────────────┐
│  🌱 Tomato Seeds        │
│  Vegetable              │
│  ★★★★★ (12)            │
│  Jan – Mar              │
│  60 days to grow        │
│  👤 Juan Dela Cruz  ←── NEW: Clickable seller link
│  ₱50.00 / pack          │
│  [View] [Add to Cart]   │
└─────────────────────────┘
```

## 2. Seed Details Page (Updated)

### New Seller Card in Sidebar:
```
┌─────────────────────────────┐
│  SELLER INFORMATION         │
├─────────────────────────────┤
│  [Avatar]  Juan Dela Cruz   │
│            juan@email.com   │
│                             │
│  📦 12 Active Listings      │
│  📅 Member since Jan 2024   │
│                             │
│  [View Profile] ←────────── NEW: Button to profile
└─────────────────────────────┘
```

## 3. Seller Profile Page (NEW)

```
┌────────────────────────────────────────────────────────┐
│  SELLER PROFILE                                        │
├────────────────────────────────────────────────────────┤
│                                                        │
│  [Avatar]  Juan Dela Cruz                             │
│            juan@email.com                             │
│            123 Main St, Brgy. Sample, Manila          │
│            Member since: January 2024                 │
│                                                        │
├────────────────────────────────────────────────────────┤
│  STATISTICS                                           │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐          │
│  │ 📦 12    │  │ 🛒 45    │  │ ⭐ 4.5   │          │
│  │ Listings │  │ Sales    │  │ Rating   │          │
│  └──────────┘  └──────────┘  └──────────┘          │
│                                                        │
├────────────────────────────────────────────────────────┤
│  ACTIVE LISTINGS                                      │
│                                                        │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐             │
│  │ 🌱      │  │ 🌱      │  │ 🌱      │             │
│  │ Tomato  │  │ Carrot  │  │ Lettuce │             │
│  │ ₱50.00  │  │ ₱35.00  │  │ ₱40.00  │             │
│  │ [View]  │  │ [View]  │  │ [View]  │             │
│  └─────────┘  └─────────┘  └─────────┘             │
│                                                        │
└────────────────────────────────────────────────────────┘
```

## Color Scheme

All components use the SeedCycle theme:

- **Primary Green**: `#2E7D32` - Headers, primary text
- **Light Green**: `#4CAF50` - Buttons, accents
- **Yellow**: `#FFC107` - Highlights, ratings
- **Background**: `#F5F5DC` - Beige page background
- **Cards**: `#FFFFFF` - White card backgrounds
- **Borders**: `#c8e6c9` - Light green borders

## Interactive Elements

### Seller Link on Marketplace Card
- **Default**: Green text (#2E7D32) with user icon
- **Hover**: Light green background (#e8f5e9), darker text (#1B5E20)
- **Click**: Navigate to `seller-profile.php?id={seller_id}`

### View Profile Button
- **Default**: Green background (#2E7D32), white text
- **Hover**: Darker green (#256427)
- **Click**: Navigate to seller profile page

### Listing Cards on Profile
- **Default**: White background, green border
- **Hover**: Shadow effect (0 4px 16px rgba(46,125,50,0.1))
- **Click**: Navigate to seed details page

## Responsive Design

### Desktop (>768px)
- Marketplace: 4-5 cards per row
- Seller Profile: 3-4 listing cards per row
- Full sidebar with seller card

### Mobile (<768px)
- Marketplace: 1-2 cards per row
- Seller Profile: 1-2 listing cards per row
- Stacked layout for better readability
- Touch-friendly buttons (48x48px minimum)

## Database Schema Used

### Tables Involved:
1. **users** - Seller information (name, email, avatar, address)
2. **seed_listings** - Links sellers to their seeds
3. **inventory** - Seed details (name, price, category, etc.)
4. **orders** + **order_items** - Sales statistics
5. **reviews** - Rating calculations

### Key Queries:
- Marketplace: JOIN inventory + seed_listings + users
- Seller Profile: Aggregate stats from multiple tables
- Seller Listings: Filter by user_id and status="approved"

## File Structure

```
SeedCycle/
├── app/
│   ├── Controllers/
│   │   └── SeedController.php (updated)
│   ├── Models/
│   │   └── Seed.php (updated)
│   └── Views/
│       ├── marketplace.php (updated)
│       ├── seller-profile.php (new)
│       └── seeds/
│           └── details.php (updated)
├── public/
│   ├── seller-profile.php (new)
│   └── assets/
│       └── css/
│           ├── marketplace.css (updated)
│           ├── seed-details.css (updated)
│           └── seller-profile.css (new)
└── SELLER_PROFILE_FEATURE.md (documentation)
```

## Testing URLs

1. **Marketplace**: `http://localhost/SeedCycle/public/marketplace.php`
   - Look for seller names below each seed card
   - Click seller name to visit profile

2. **Seed Details**: `http://localhost/SeedCycle/public/seed-details.php?id=1`
   - Look for seller card in sidebar
   - Click "View Profile" button

3. **Seller Profile**: `http://localhost/SeedCycle/public/seller-profile.php?id=1`
   - View seller information and statistics
   - Browse seller's listings
   - Click any listing to view details

## Implementation Status

✅ **COMPLETE** - All features implemented and tested
- Seller links on marketplace cards
- Seller card on seed details page
- Full seller profile page with statistics
- Responsive design for all screen sizes
- SeedCycle theme consistency maintained
