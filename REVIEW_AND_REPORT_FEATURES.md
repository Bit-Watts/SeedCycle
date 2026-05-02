# Review and Report Features - Implementation Guide

## Overview
Your SeedCycle application already has a fully functional **Review** and **Report** system implemented. This document explains how these features work and where they're located.

---

## 🌟 Review Feature

### What It Does
- Allows buyers to rate and review seeds they've purchased and received
- Displays average ratings and review counts
- Shows individual reviews with user details and timestamps
- Prevents duplicate reviews from the same user
- Only allows reviews from verified purchasers (delivered orders)

### Key Files

#### Controllers
- **`app/Controllers/ReviewController.php`**
  - `submit()` - Handles review submission
  - `delete()` - Allows users to delete their own reviews
  - Validates that user has purchased and received the seed

#### Models
- **`app/Models/Review.php`**
  - `getByInventory()` - Fetches all reviews for a seed
  - `getAverageRating()` - Calculates average rating and total count
  - `hasReviewed()` - Checks if user already reviewed
  - `hasPurchased()` - Verifies user purchased and received the seed
  - `create()` - Creates new review
  - `delete()` - Deletes review

#### Views
- **`app/Views/seeds/details.php`** - Displays reviews section with:
  - Rating summary (average rating + total reviews)
  - Review submission form (for eligible buyers)
  - List of all reviews with user info
  - Star rating display

#### Public Endpoints
- **`public/review-submit.php`** - Processes review submissions
- **`public/review-delete.php`** - Processes review deletions
- **`public/seed-details.php`** - Shows seed details with reviews

### How It Works

1. **User purchases a seed** → Order is placed
2. **Order is delivered** → User becomes eligible to review
3. **User visits seed details page** → Sees "Write a Review" form
4. **User submits review** → Rating (1-5 stars) + optional comment
5. **Review appears** → Displayed on seed details page for all users

### Validation Rules
- ✅ User must be logged in
- ✅ User must have purchased the seed
- ✅ Order must be marked as "delivered"
- ✅ User can only review once per seed
- ✅ Rating must be between 1-5 stars
- ✅ Users cannot review their own seed listings

---

## 🚩 Report Feature

### What It Does
- Allows users to report problematic seeds or reviews
- Supports multiple report reasons (spam, fake, inappropriate, etc.)
- Prevents duplicate reports from the same user
- Admin can view and manage reports

### Key Files

#### Controllers
- **`app/Controllers/ReviewController.php`**
  - `report()` - Handles report submissions for both seeds and reviews

#### Models
- **`app/Models/Report.php`**
  - `create()` - Creates new report
  - `alreadyReported()` - Prevents duplicate reports
  - `getAll()` - Fetches all reports (for admin)
  - `updateStatus()` - Updates report status (pending/resolved/dismissed)
  - `getPendingCount()` - Counts pending reports

#### Views
- **`app/Views/seeds/details.php`** - Contains:
  - "Report this seed" button
  - "Report" button on each review
  - Report modal with form

#### Public Endpoints
- **`public/report-submit.php`** - Processes report submissions

### Report Types
1. **Seed Reports** - Report problematic seed listings
2. **Review Reports** - Report inappropriate reviews

### Report Reasons
- `spam` - Spam content
- `fake` - Fake or misleading information
- `inappropriate` - Inappropriate content
- `wrong_info` - Wrong information
- `other` - Other reasons

### How It Works

1. **User sees problematic content** → Clicks "Report" button
2. **Report modal opens** → User selects reason + adds details
3. **Report submitted** → Stored with status "pending"
4. **Admin reviews** → Can view all reports in admin panel
5. **Admin takes action** → Updates status to resolved/dismissed

### Validation Rules
- ✅ User must be logged in
- ✅ User cannot report their own seeds
- ✅ User can only report once per item
- ✅ Must select a valid report reason
- ✅ Report type must be 'seed' or 'review'

---

## 📊 Database Schema

### Reviews Table
```sql
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    inventory_id INT NOT NULL,
    order_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (inventory_id) REFERENCES inventory(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
```

### Reports Table
```sql
CREATE TABLE reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reporter_id INT NOT NULL,
    reported_user_id INT NULL,  -- For review reports
    inventory_id INT NULL,       -- For seed reports
    reason VARCHAR(50) NOT NULL,
    description TEXT,
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id)
);
```

---

## 🎨 UI Components

### Review Section (on Seed Details Page)
- **Rating Summary** - Large average rating with star display
- **Write Review Form** - Star rating input + comment textarea
- **Reviews List** - Cards showing:
  - User avatar and name
  - Star rating
  - Comment text
  - Date posted
  - Report button

### Report Modal
- **Reason Dropdown** - Select from predefined reasons
- **Details Textarea** - Optional additional information
- **Submit/Cancel Buttons** - Form actions

### Styling
- **CSS Files:**
  - `public/assets/css/seed-details.css` - Seed details page styles
  - `public/assets/css/reviews.css` - Review-specific styles

---

## 🔧 Usage Examples

### Viewing Reviews
```
Navigate to: public/seed-details.php?id=1
```
- See all reviews for seed ID 1
- View average rating
- Read individual reviews

### Submitting a Review
```
1. Purchase a seed
2. Wait for order to be delivered
3. Visit seed details page
4. Fill out "Write a Review" form
5. Submit
```

### Reporting Content
```
1. Click "Report this seed" or "Report" on a review
2. Select reason from dropdown
3. Add optional details
4. Click "Submit Report"
```

---

## ✅ Features Already Implemented

✅ **Review System**
- Star ratings (1-5)
- Text comments
- Average rating calculation
- Review count display
- Purchase verification
- Duplicate prevention
- User profile integration

✅ **Report System**
- Seed reporting
- Review reporting
- Multiple report reasons
- Duplicate prevention
- Admin management interface
- Status tracking (pending/resolved/dismissed)

✅ **Security**
- Authentication required
- Ownership validation
- SQL injection prevention (prepared statements)
- XSS prevention (htmlspecialchars)
- Input validation

✅ **User Experience**
- Modal dialogs
- Flash messages (success/error)
- Responsive design
- Star rating visualization
- User avatars in reviews

---

## 🎯 Next Steps (Optional Enhancements)

If you want to extend these features, consider:

1. **Review Editing** - Allow users to edit their reviews
2. **Review Voting** - Helpful/Not Helpful buttons
3. **Review Images** - Allow photo uploads with reviews
4. **Email Notifications** - Notify sellers of new reviews
5. **Report Analytics** - Dashboard showing report trends
6. **Auto-moderation** - Flag reviews with certain keywords
7. **Review Responses** - Allow sellers to respond to reviews
8. **Verified Purchase Badge** - Show badge on verified purchases

---

## 📝 Summary

Your application already has a complete review and report system! Users can:
- ⭐ Rate and review seeds they've purchased
- 📊 See average ratings and review counts
- 🚩 Report problematic seeds or reviews
- 👀 View all reviews on seed detail pages

The system includes proper validation, security measures, and a clean UI. All the code is in place and functional.
