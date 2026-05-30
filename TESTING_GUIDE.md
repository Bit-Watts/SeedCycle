# SeedCycle Testing Guide

## 🧪 Complete Testing Checklist

This guide helps you test all features of SeedCycle, including the new WebSocket real-time messaging system.

## 📋 Pre-Testing Setup

### 1. Environment Check
- [ ] XAMPP/WAMP running
- [ ] MySQL database `seed cycle` exists
- [ ] All SQL files imported
- [ ] Composer dependencies installed
- [ ] WebSocket server running (`php websocket-server.php`)

### 2. Browser Setup
- [ ] Open browser console (F12)
- [ ] Check for JavaScript errors
- [ ] Verify WebSocket connection: Look for `✓ WebSocket connected`

## 🔐 Authentication Tests

### Registration
1. Go to signup page
2. Fill in all fields
3. Upload profile picture
4. Submit form
5. **Expected**: Account created, redirected to login

### Login
1. Enter valid credentials
2. Click login
3. **Expected**: Redirected to dashboard

### Logout
1. Click logout button
2. **Expected**: Redirected to login page

## 🛒 Marketplace Tests

### Browse Seeds
1. Go to marketplace
2. **Expected**: See seed listings
3. Use filters (category, price)
4. **Expected**: Results update

### View Seed Details
1. Click on a seed
2. **Expected**: See full details, images, reviews
3. Check stock availability
4. **Expected**: Stock count displayed

### Add to Cart
1. Select quantity
2. Click "Add to Cart"
3. **Expected**: Success message, cart icon updates

## 🛍️ Shopping Cart Tests

### View Cart
1. Click cart icon
2. **Expected**: See all cart items
3. Check quantities and prices
4. **Expected**: Total calculated correctly

### Update Quantity
1. Change item quantity
2. Click update
3. **Expected**: Total recalculates

### Remove Item
1. Click remove button
2. **Expected**: Item removed, total updates

## 💳 Checkout Tests

### COD Checkout
1. Go to cart, click checkout
2. Fill in delivery address
3. Select "Cash on Delivery"
4. Submit order
5. **Expected**: 
   - Order created
   - Redirected to success page
   - Cart cleared
   - **WebSocket**: Seller receives notification

### GCash Checkout
1. Go to cart, click checkout
2. Fill in delivery address
3. Select "GCash"
4. Submit order
5. **Expected**: Redirected to GCash payment page
6. Enter reference number
7. Submit payment
8. **Expected**: 
   - Payment processed
   - Redirected to success page
   - **WebSocket**: Seller receives notification

### Address Auto-Fill
1. On checkout page
2. Click "Use my saved address"
3. **Expected**: All address fields populated

## 📦 Order Management Tests (Buyer)

### View Orders
1. Go to "My Orders"
2. **Expected**: See all orders in accordion format
3. Click to expand order
4. **Expected**: See order details

### Track Order
1. Click "Track Order"
2. **Expected**: See shipment status timeline
3. Check tracking number
4. **Expected**: Tracking info displayed

### Real-Time Updates (WebSocket)
1. Have seller update shipment status
2. **Expected**: 
   - Notification badge updates immediately
   - No page refresh needed
   - Toast/alert appears

## 📤 Seller Features Tests

### List Seeds
1. Go to "Sell Seeds"
2. Fill in seed details
3. Upload image
4. Submit listing
5. **Expected**: 
   - Listing created
   - Status: "Pending Approval"

### View Seller Orders
1. Go to "Seller Orders"
2. **Expected**: See orders containing your seeds
3. Check order details
4. **Expected**: Buyer info, items, total

### Create Shipment
1. Find pending order
2. Click "Create Shipment"
3. Fill in:
   - Courier (e.g., LBC)
   - Tracking number
   - Estimated delivery
   - Status
4. Submit
5. **Expected**: 
   - Shipment created
   - **WebSocket**: Buyer receives notification immediately
   - Check buyer's notification badge updates

### Update Shipment
1. Find existing shipment
2. Click "Update Shipment"
3. Change status (e.g., "Shipped" → "In Transit")
4. Submit
5. **Expected**: 
   - Status updated
   - **WebSocket**: Buyer receives notification
   - Buyer sees update without refresh

## 💬 Real-Time Messaging Tests (WebSocket)

### Access Chat
1. Click messages icon in navbar
2. **Expected**: See chat page with conversations list
3. Check notification badge
4. **Expected**: Shows unread count

### Send Message
1. Select a conversation
2. Type message
3. Click send
4. **Expected**: 
   - Message appears immediately
   - Other user receives instantly (test with 2 browsers)
   - No page refresh needed

### Typing Indicator
1. Start typing in message box
2. **Expected**: 
   - Other user sees "typing..." indicator
   - Indicator disappears after 2 seconds

### Read Receipts
1. Send message
2. Other user opens conversation
3. **Expected**: Message marked as read

### Order Update in Chat
1. Seller updates shipment
2. **Expected**: 
   - System message appears in chat
   - Shows order status update
   - Buyer receives notification

### Two-Browser Test
**Setup**: Open 2 browsers (or incognito)
- Browser A: Login as buyer
- Browser B: Login as seller

**Test Flow**:
1. Buyer places order
2. **Expected**: Seller sees notification immediately in Browser B
3. Seller creates shipment
4. **Expected**: Buyer sees notification immediately in Browser A
5. Seller sends chat message
6. **Expected**: Buyer receives message instantly
7. Buyer replies
8. **Expected**: Seller receives reply instantly

## 🔔 Notifications Tests

### View Notifications
1. Click notification badge
2. Go to notifications page
3. **Expected**: See all notifications
4. Check unread highlighting
5. **Expected**: Unread items have green background

### Mark as Read
1. Click "Mark as Read" on notification
2. **Expected**: 
   - Background changes to white
   - Badge count decreases

### View Order from Notification
1. Click "View Order" button
2. **Expected**: Redirected to order tracking page

## 🌤️ Weather & Planting Guide Tests

### View Planting Guide
1. Go to "Planting Guide"
2. **Expected**: See current weather
3. Check calendar view
4. **Expected**: Crops organized by month
5. Switch to list view
6. **Expected**: All crops listed

### Weather Data
1. Check temperature display
2. Check humidity
3. Check season badge
4. **Expected**: All data from OpenWeatherMap API

## ⭐ Review System Tests

### Leave Review
1. Go to completed order
2. Click "Leave Review"
3. Rate 1-5 stars
4. Write comment
5. Submit
6. **Expected**: Review saved and displayed

### View Reviews
1. Go to seed details page
2. Scroll to reviews section
3. **Expected**: See all reviews with ratings

## 👨‍💼 Admin Tests

### Login as Admin
1. Use admin credentials
2. **Expected**: Redirected to admin dashboard

### Approve Listings
1. Go to "Manage Listings"
2. Find pending listing
3. Click "Approve"
4. **Expected**: Status changes to "Approved"

### Manage Users
1. Go to "Manage Users"
2. View user list
3. **Expected**: See all registered users

### View Reports
1. Go to "Reports"
2. **Expected**: See sales data, statistics

## 🐛 Error Handling Tests

### Invalid Login
1. Enter wrong password
2. **Expected**: Error message displayed

### Empty Cart Checkout
1. Clear cart
2. Try to checkout
3. **Expected**: Error message

### Insufficient Stock
1. Add more items than available
2. Try to checkout
3. **Expected**: Error message

### WebSocket Disconnection
1. Stop WebSocket server
2. Try to send message
3. **Expected**: 
   - Error message
   - "Connection Lost" banner
   - Auto-reconnect attempts

### Network Error
1. Disconnect internet
2. Try to place order
3. **Expected**: Error message

## 📱 Responsive Design Tests

### Mobile View (< 768px)
1. Resize browser to mobile width
2. Test navigation
3. Test forms
4. Test chat interface
5. **Expected**: All elements responsive

### Tablet View (768px - 1024px)
1. Resize to tablet width
2. Test all pages
3. **Expected**: Proper layout

### Desktop View (> 1024px)
1. Full screen
2. Test all features
3. **Expected**: Optimal layout

## 🔒 Security Tests

### SQL Injection
1. Try entering SQL in forms: `' OR '1'='1`
2. **Expected**: Input sanitized, no injection

### XSS Attack
1. Try entering script: `<script>alert('XSS')</script>`
2. **Expected**: Script escaped, not executed

### Unauthorized Access
1. Logout
2. Try accessing protected pages directly
3. **Expected**: Redirected to login

### File Upload
1. Try uploading non-image file
2. **Expected**: Error message
3. Try uploading large file (> 5MB)
4. **Expected**: Error message

## ⚡ Performance Tests

### Page Load Time
1. Clear cache
2. Load marketplace
3. **Expected**: < 3 seconds

### WebSocket Connection
1. Open console
2. Check connection time
3. **Expected**: < 1 second

### Message Delivery
1. Send chat message
2. Measure time to appear
3. **Expected**: < 500ms

### Database Queries
1. Check server logs
2. **Expected**: No slow queries (> 1s)

## ✅ Final Checklist

### Core Features
- [ ] User registration works
- [ ] Login/logout works
- [ ] Browse marketplace works
- [ ] Add to cart works
- [ ] Checkout works (COD & GCash)
- [ ] Order tracking works
- [ ] Seller can list seeds
- [ ] Seller can manage orders
- [ ] Admin can approve listings

### WebSocket Features
- [ ] WebSocket server starts successfully
- [ ] Browser connects to WebSocket
- [ ] Chat messages send/receive instantly
- [ ] Typing indicators work
- [ ] Read receipts work
- [ ] Order notifications appear in real-time
- [ ] Notification badge updates automatically
- [ ] Auto-reconnection works
- [ ] Two-browser test passes

### UI/UX
- [ ] All pages load correctly
- [ ] No console errors
- [ ] Responsive on mobile
- [ ] Animations smooth
- [ ] Forms validate properly
- [ ] Error messages clear

### Data Integrity
- [ ] Orders save correctly
- [ ] Inventory updates properly
- [ ] Messages persist in database
- [ ] Notifications saved
- [ ] No data loss on refresh

## 🎯 Success Criteria

All tests should pass with:
- ✅ No JavaScript errors in console
- ✅ No PHP errors in logs
- ✅ WebSocket connection stable
- ✅ Real-time updates working
- ✅ All CRUD operations functional
- ✅ Responsive design working
- ✅ Security measures effective

## 📞 Reporting Issues

When reporting bugs, include:
1. **Steps to reproduce**
2. **Expected behavior**
3. **Actual behavior**
4. **Browser console errors**
5. **Server logs** (if applicable)
6. **Screenshots**

---

**Happy Testing! 🧪**
