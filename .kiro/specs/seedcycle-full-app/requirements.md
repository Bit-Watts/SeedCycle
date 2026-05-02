# Requirements Document

## Introduction

SeedCycle is a PHP MVC web application that connects seed buyers and sellers in a local agricultural marketplace. The platform allows users to browse and purchase seeds, track orders, access planting guides, and submit seeds for sale. Administrators manage inventory, users, listings, orders, and shipments through a dedicated admin panel. The system is built on an existing partial codebase with Controllers, Models, Views, and a MySQL database already partially implemented.

## Glossary

- **System**: The SeedCycle web application
- **User**: An authenticated registered member with role = 'user'
- **Admin**: An authenticated registered member with role = 'admin'
- **Guest**: An unauthenticated visitor
- **Inventory**: The `inventory` database table containing seeds managed by admins
- **Cart**: The `cart` database table (or session-based cart) holding items a User intends to purchase
- **Order**: A confirmed purchase record stored in the `orders` table with associated `order_items`
- **Listing**: A seller request stored in `seed_listings` awaiting admin approval
- **Shipment**: A delivery record in the `shipments` table containing courier and tracking information
- **Planting_Guide**: The feature that displays planting schedules derived from `inventory` planting month fields
- **AuthController**: The controller handling login, signup, and logout
- **DashboardController**: The controller handling the user dashboard, profile, and settings
- **SeedController**: The controller handling marketplace, seed details, sell seeds, and my seeds
- **OrderController**: The controller handling order listing and checkout
- **AdminController**: The controller handling all admin panel operations
- **CartController**: The controller handling cart operations
- **User_Model**: The `User` model class
- **Seed_Model**: The `Seed` model class
- **Order_Model**: The `Order` model class
- **SeedListing_Model**: The `SeedListing` model class
- **Shipment_Model**: The `Shipment` model class

---

## Requirements

### Requirement 1: Landing Page

**User Story:** As a Guest, I want to see an informative landing page, so that I can understand what SeedCycle offers and decide to sign up or log in.

#### Acceptance Criteria

1. THE System SHALL display a landing page at `index.php` with a hero section, feature highlights (buy seeds, sell seeds, planting guide), and call-to-action buttons linking to `signup.php` and `login.php`.
2. WHEN a Guest clicks the Sign Up call-to-action, THE System SHALL navigate to `signup.php`.
3. WHEN a Guest clicks the Login call-to-action, THE System SHALL navigate to `login.php`.
4. WHEN an authenticated User visits `index.php`, THE System SHALL redirect to `dashboard.php`.

---

### Requirement 2: User Authentication — Login

**User Story:** As a registered User, I want to log in with my email and password, so that I can access my account.

#### Acceptance Criteria

1. THE System SHALL display a login form at `login.php` with fields for email and password.
2. WHEN a User submits valid credentials, THE AuthController SHALL verify the password hash against `users.password_hash` and create a session with `user_id`, `first_name`, `last_name`, `username`, `email`, and `role`.
3. WHEN a User with `role = 'admin'` successfully logs in, THE AuthController SHALL redirect to `admin/dashboard.php`.
4. WHEN a User with `role = 'user'` successfully logs in, THE AuthController SHALL redirect to `dashboard.php`.
5. IF the submitted email does not exist or the password does not match, THEN THE AuthController SHALL redisplay the login form with the error message "Invalid email or password."
6. IF a User account has `is_active = 0`, THEN THE AuthController SHALL reject the login and display the message "Your account has been deactivated."
7. WHEN an already-authenticated User visits `login.php`, THE System SHALL redirect to `dashboard.php`.

---

### Requirement 3: User Authentication — Sign Up

**User Story:** As a Guest, I want to register a new account, so that I can buy and sell seeds on SeedCycle.

#### Acceptance Criteria

1. THE System SHALL display a registration form at `signup.php` with fields for first name, last name, email, username, password, and confirm password.
2. WHEN a Guest submits the registration form with all valid fields, THE AuthController SHALL hash the password and insert a new record into the `users` table with `role = 'user'` and `is_active = 1`.
3. IF the submitted email or username already exists in the `users` table, THEN THE AuthController SHALL redisplay the form with the error "Email or username is already taken."
4. IF the password is fewer than 6 characters, THEN THE AuthController SHALL redisplay the form with the error "Password must be at least 6 characters."
5. IF the password and confirm password fields do not match, THEN THE AuthController SHALL redisplay the form with the error "Passwords do not match."
6. WHEN registration succeeds, THE AuthController SHALL display a success message with a link to `login.php`.

---

### Requirement 4: User Home / Dashboard

**User Story:** As an authenticated User, I want a personalized dashboard, so that I can quickly access key features and see relevant information.

#### Acceptance Criteria

1. WHEN an authenticated User visits `dashboard.php`, THE DashboardController SHALL load the user record from `users` and render the dashboard view.
2. THE System SHALL display the User's first name in a greeting, a sidebar navigation, and summary stat cards (seeds listed, orders placed, orders received, notifications).
3. THE System SHALL display a "Recommended Seeds" section populated from the `inventory` table showing up to 4 active, in-stock seeds.
4. THE System SHALL display a "Planting Schedule Preview" section showing the current and next two calendar months with seeds plantable in those months from `inventory`.
5. IF the session is invalid or expired, THEN THE DashboardController SHALL redirect to `login.php`.

---

### Requirement 5: Marketplace Page

**User Story:** As a User, I want to browse all available seeds, so that I can find seeds to purchase.

#### Acceptance Criteria

1. WHEN a User visits `marketplace.php`, THE SeedController SHALL query all active, in-stock seeds from `inventory` and render the marketplace view.
2. THE System SHALL display each seed as a card showing name, category, planting months, growing days, price per pack, a "View" link to `seed-details.php?id={id}`, and an "Add to Cart" button.
3. WHEN a User types in the search input, THE System SHALL filter visible seed cards client-side by seed name in real time.
4. WHEN a User selects a category filter and clicks "Apply Filters", THE System SHALL filter visible seed cards client-side by category and maximum price.
5. WHEN a User clicks "Add to Cart" on a seed card, THE CartController SHALL add the seed to the User's cart (database `cart` table) and redirect to `cart.php`.
6. IF no seeds match the active filters, THE System SHALL display an empty-state message "No seeds match your search."

---

### Requirement 6: Seed Details Page

**User Story:** As a User, I want to view full details of a seed, so that I can make an informed purchase decision.

#### Acceptance Criteria

1. WHEN a User visits `seed-details.php?id={id}`, THE SeedController SHALL query the seed from `inventory` by ID and render the details view.
2. THE System SHALL display the seed name, category, description, price, stock quantity, planting start month, planting end month, and growing days.
3. THE System SHALL display all images for the seed from the `seed_images` table where `inventory_id` matches.
4. WHEN a User clicks "Add to Cart" on the details page, THE CartController SHALL add the seed with the selected quantity to the `cart` table and redirect to `cart.php`.
5. IF the seed ID does not exist or `is_active = 0`, THEN THE SeedController SHALL redirect to `marketplace.php`.

---

### Requirement 7: Cart Page

**User Story:** As a User, I want to manage my cart, so that I can review and adjust items before checkout.

#### Acceptance Criteria

1. WHEN a User visits `cart.php`, THE CartController SHALL query all cart items for the User from the `cart` table joined with `inventory` and render the cart view.
2. THE System SHALL display each cart item with seed name, category, unit price, quantity controls, subtotal, and a remove button.
3. THE System SHALL display an order summary with per-item subtotals and a grand total.
4. WHEN a User changes the quantity of a cart item and submits the update, THE CartController SHALL update the `quantity` field in the `cart` table for that item.
5. WHEN a User clicks the remove button for a cart item, THE CartController SHALL delete that row from the `cart` table and reload the cart view.
6. WHEN a User clicks "Proceed to Checkout", THE System SHALL navigate to `checkout.php`.
7. IF the cart is empty, THE System SHALL display an empty-state message and a link to `marketplace.php`.

---

### Requirement 8: Checkout Page

**User Story:** As a User, I want to enter delivery details and confirm my order, so that I can complete my purchase.

#### Acceptance Criteria

1. WHEN a User visits `checkout.php`, THE OrderController SHALL load the User's cart items and render the checkout form.
2. THE System SHALL display a checkout form with fields for barangay, city, municipality, province, zip code, and delivery method (pickup or delivery).
3. THE System SHALL display an order summary showing all cart items, quantities, unit prices, and the grand total.
4. WHEN a User submits the checkout form with all required fields, THE OrderController SHALL insert a record into `orders` (with `user_id`, `total_amount`, `status = 'pending'`, `delivery_method`, and address fields) and insert one row per cart item into `order_items` (with `order_id`, `inventory_id`, `quantity`, `price`).
5. WHEN the order is saved successfully, THE OrderController SHALL decrement `stock_quantity` in `inventory` for each ordered item, clear the User's rows from the `cart` table, and redirect to `orders.php`.
6. IF any required checkout field is empty, THEN THE OrderController SHALL redisplay the form with the error "Please fill in all required fields."
7. IF a cart item's `inventory_id` has insufficient `stock_quantity` at time of checkout, THEN THE OrderController SHALL redisplay the form with an error identifying the out-of-stock seed.

---

### Requirement 9: My Orders Page

**User Story:** As a User, I want to view my order history, so that I can track the status of my purchases.

#### Acceptance Criteria

1. WHEN a User visits `orders.php`, THE OrderController SHALL query all orders for the User from `orders` joined with `order_items` and `inventory` and render the orders view.
2. THE System SHALL display each order with order ID, seed names, total amount, order status, shipping status, and order date.
3. THE System SHALL display order status using the values: pending, confirmed, shipped, out_for_delivery, delivered.
4. WHEN a User clicks on an order row, THE System SHALL navigate to `order-tracking.php?id={order_id}`.

---

### Requirement 10: Order Tracking Page

**User Story:** As a User, I want to see shipment details for my order, so that I can know when my seeds will arrive.

#### Acceptance Criteria

1. WHEN a User visits `order-tracking.php?id={order_id}`, THE OrderController SHALL query the order from `orders` and its shipment from `shipments` where `order_id` matches and render the tracking view.
2. THE System SHALL display the order status, shipping status, courier name, tracking number, and estimated delivery date from the `shipments` table.
3. THE System SHALL display a visual status timeline showing the progression: pending → confirmed → shipped → out_for_delivery → delivered.
4. IF no shipment record exists for the order, THE System SHALL display "Shipment details not yet available."
5. IF the order does not belong to the authenticated User, THEN THE OrderController SHALL redirect to `orders.php`.

---

### Requirement 11: Planting Guide Page

**User Story:** As a User, I want to view a planting schedule, so that I can know the best months to plant each seed.

#### Acceptance Criteria

1. WHEN a User visits `planting-guide.php`, THE SeedController SHALL query all active seeds from `inventory` that have non-null `planting_start_month` and `planting_end_month` values and render the planting guide view.
2. THE System SHALL display each seed with its name, category, planting start month, planting end month, and growing days.
3. THE System SHALL display a month-based calendar grid showing which seeds are plantable in each month of the year.
4. WHEN a User selects a month filter, THE System SHALL filter the displayed seeds to only those whose planting window includes the selected month.

---

### Requirement 12: Sell Seeds Page

**User Story:** As a User, I want to submit a request to list a seed for sale, so that I can participate as a seller on the marketplace.

#### Acceptance Criteria

1. WHEN an authenticated User visits `sell-seeds.php`, THE SeedController SHALL load all active seeds from `inventory` and render the sell seeds form.
2. THE System SHALL display a form with a dropdown to select a seed from `inventory` and a submit button.
3. WHEN a User submits the form with a valid seed selection, THE SeedController SHALL insert a record into `seed_listings` with `user_id`, `inventory_id`, and `status = 'pending'`.
4. IF the User already has a pending listing request for the selected seed, THEN THE SeedController SHALL redisplay the form with the error "You already have a pending request for this seed."
5. WHEN the listing request is submitted successfully, THE SeedController SHALL display the success message "Your listing request has been submitted and is pending approval."
6. IF no seed is selected, THEN THE SeedController SHALL redisplay the form with the error "Please select a seed."

---

### Requirement 13: User Profile Page

**User Story:** As a User, I want to view and edit my profile information, so that I can keep my account details up to date.

#### Acceptance Criteria

1. WHEN an authenticated User visits `profile.php`, THE DashboardController SHALL load the User record from `users` and render the profile view.
2. THE System SHALL display the User's first name, last name, username, email, phone number, address, profile image, and account creation date.
3. WHEN a User submits the profile update form with valid data, THE DashboardController SHALL update `first_name`, `last_name`, `username`, `email`, `phone_number`, and `address` in the `users` table.
4. IF the new email or username is already taken by another account, THEN THE DashboardController SHALL redisplay the form with the error "Email or username is already taken by another account."
5. WHEN a User submits the change password form, THE DashboardController SHALL verify the current password, then update `password_hash` in `users` if the new password is at least 6 characters and the confirmation matches.
6. IF the current password is incorrect, THEN THE DashboardController SHALL display the error "Current password is incorrect."
7. WHEN a User uploads a profile image, THE System SHALL store the image file in `public/assets/uploads/` and update `profile_image` in the `users` table.

---

### Requirement 14: Favorites Page (Optional)

**User Story:** As a User, I want to save seeds to a favorites list, so that I can quickly find seeds I am interested in.

#### Acceptance Criteria

1. WHEN an authenticated User visits `favorites.php`, THE System SHALL query all rows from the `favorites` table for the User joined with `inventory` and render the favorites view.
2. WHEN a User clicks "Add to Favorites" on a seed card or details page, THE System SHALL insert a row into `favorites` with `user_id` and `inventory_id` if it does not already exist.
3. WHEN a User clicks "Remove from Favorites", THE System SHALL delete the corresponding row from `favorites`.
4. IF the favorites list is empty, THE System SHALL display an empty-state message and a link to `marketplace.php`.

---

### Requirement 15: Admin Dashboard

**User Story:** As an Admin, I want an overview of platform activity, so that I can monitor the health of the marketplace.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/dashboard.php`, THE AdminController SHALL query aggregate counts and render the admin dashboard view.
2. THE System SHALL display total registered users (count of `users`), total orders (count of `orders`), total active seeds (count of `inventory` where `is_active = 1`), and total pending listings (count of `seed_listings` where `status = 'pending'`).
3. THE System SHALL display a recent orders table showing the 10 most recent orders with order ID, user name, total amount, status, and date.
4. IF a non-Admin User attempts to access any `admin/` page, THEN THE AdminController SHALL redirect to `dashboard.php`.

---

### Requirement 16: Admin — Manage Users

**User Story:** As an Admin, I want to view and manage user accounts, so that I can activate or deactivate users as needed.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/users.php`, THE AdminController SHALL query all records from `users` and render the manage users view.
2. THE System SHALL display each user with ID, full name, username, email, role, account status (`is_active`), and registration date.
3. WHEN an Admin clicks "Deactivate" for an active user, THE AdminController SHALL set `is_active = 0` for that user in the `users` table.
4. WHEN an Admin clicks "Activate" for an inactive user, THE AdminController SHALL set `is_active = 1` for that user in the `users` table.
5. THE System SHALL prevent an Admin from deactivating their own account.

---

### Requirement 17: Admin — Manage Seeds (Inventory)

**User Story:** As an Admin, I want to add, edit, and delete seeds in the inventory, so that I can keep the marketplace catalog up to date.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/seeds.php`, THE AdminController SHALL query all records from `inventory` and render the manage seeds view.
2. THE System SHALL display each seed with ID, name, category, price, stock quantity, active status, and action buttons (Edit, Delete).
3. WHEN an Admin submits the add seed form with name, category, description, price, stock quantity, planting start month, planting end month, and growing days, THE AdminController SHALL insert a new record into `inventory` with `is_active = 1`.
4. WHEN an Admin submits the edit seed form, THE AdminController SHALL update the corresponding record in `inventory`.
5. WHEN an Admin clicks "Delete" for a seed, THE AdminController SHALL set `is_active = 0` for that seed (soft delete).
6. WHEN an Admin uploads images for a seed, THE System SHALL store each image in `public/assets/uploads/seeds/` and insert a row into `seed_images` with `inventory_id` and `image_path`.

---

### Requirement 18: Admin — Manage Listings

**User Story:** As an Admin, I want to approve or reject seed listing requests, so that I can control what appears on the marketplace.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/listings.php`, THE AdminController SHALL query all records from `seed_listings` joined with `users` and `inventory` and render the manage listings view.
2. THE System SHALL display each listing with listing ID, seller name, seed name, submission date, and current status.
3. WHEN an Admin clicks "Approve" for a pending listing, THE AdminController SHALL update `seed_listings.status` to `'approved'`.
4. WHEN an Admin clicks "Reject" for a pending listing, THE AdminController SHALL update `seed_listings.status` to `'rejected'`.
5. THE System SHALL display listings grouped by status: pending, approved, rejected.

---

### Requirement 19: Admin — Manage Orders

**User Story:** As an Admin, I want to view all orders and update their status, so that I can manage order fulfillment.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/orders.php`, THE AdminController SHALL query all records from `orders` joined with `users` and render the manage orders view.
2. THE System SHALL display each order with order ID, buyer name, total amount, order status, shipping status, delivery method, and order date.
3. WHEN an Admin selects a new order status from the dropdown and submits, THE AdminController SHALL update `orders.status` to the selected value (pending, confirmed, shipped, out_for_delivery, delivered).
4. WHEN an Admin selects a new shipping status and submits, THE AdminController SHALL update `orders.shipping_status` to the selected value.

---

### Requirement 20: Admin — Manage Shipments

**User Story:** As an Admin, I want to add and update shipment details for orders, so that buyers can track their deliveries.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/shipments.php`, THE AdminController SHALL query all records from `shipments` joined with `orders` and `users` and render the manage shipments view.
2. WHEN an Admin submits the add shipment form for an order with courier name, tracking number, and estimated delivery date, THE AdminController SHALL insert a record into `shipments` with `order_id`, `courier`, `tracking_number`, and `estimated_delivery`.
3. WHEN an Admin updates an existing shipment record, THE AdminController SHALL update the corresponding row in `shipments`.
4. WHEN a shipment status is updated to 'delivered', THE AdminController SHALL also update `orders.status` to 'delivered' for the associated order.

---

### Requirement 21: Admin — Reports and Analytics

**User Story:** As an Admin, I want to view sales summaries and user activity, so that I can make informed decisions about the marketplace.

#### Acceptance Criteria

1. WHEN an Admin visits `admin/reports.php`, THE AdminController SHALL query aggregated data and render the reports view.
2. THE System SHALL display total revenue (sum of `orders.total_amount` where `status != 'pending'`), total completed orders, and total active users.
3. THE System SHALL display a "Most Sold Seeds" table showing the top 10 seeds by total quantity sold, derived from `order_items` joined with `inventory`.
4. THE System SHALL display a "Recent Activity" log showing the 20 most recent records from `activity_logs` with user name, action, and timestamp.
5. WHERE date range filters are provided, THE System SHALL filter the revenue and order counts to the selected date range.

---

### Requirement 22: Session and Access Control

**User Story:** As a system operator, I want all protected pages to enforce authentication, so that unauthorized users cannot access private data.

#### Acceptance Criteria

1. THE System SHALL start a PHP session on every page that requires authentication before any output is sent.
2. WHEN an unauthenticated User attempts to access any page other than `index.php`, `login.php`, or `signup.php`, THE System SHALL redirect to `login.php`.
3. WHEN an authenticated User with `role = 'user'` attempts to access any `admin/` page, THE System SHALL redirect to `dashboard.php`.
4. WHEN a User logs out via `logout.php`, THE AuthController SHALL call `session_unset()` and `session_destroy()` and redirect to `login.php`.

---

### Requirement 23: Database Schema Completeness

**User Story:** As a developer, I want the database schema to support all application features, so that all data can be stored and retrieved correctly.

#### Acceptance Criteria

1. THE System SHALL use a `users` table with columns: `id`, `first_name`, `last_name`, `username`, `email`, `password_hash`, `role` (enum: 'user', 'admin'), `is_active` (tinyint, default 1), `phone_number`, `address`, `profile_image`, `created_at`.
2. THE System SHALL use an `inventory` table with columns: `id`, `name`, `category`, `description`, `price`, `stock_quantity`, `planting_start_month`, `planting_end_month`, `growing_days`, `is_active`, `created_at`.
3. THE System SHALL use an `orders` table with columns: `id`, `user_id`, `total_amount`, `status`, `shipping_status`, `delivery_method`, `barangay`, `city`, `municipality`, `province`, `zip_code`, `created_at`.
4. THE System SHALL use an `order_items` table with columns: `id`, `order_id`, `inventory_id`, `quantity`, `price`.
5. THE System SHALL use a `cart` table with columns: `id`, `user_id`, `inventory_id`, `quantity`, `added_at`.
6. THE System SHALL use a `seed_listings` table with columns: `id`, `user_id`, `inventory_id`, `status` (enum: 'pending', 'approved', 'rejected'), `created_at`.
7. THE System SHALL use a `seed_images` table with columns: `id`, `inventory_id`, `image_path`, `created_at`.
8. THE System SHALL use a `shipments` table with columns: `id`, `order_id`, `courier`, `tracking_number`, `estimated_delivery`, `status`, `created_at`.
9. THE System SHALL use a `favorites` table with columns: `id`, `user_id`, `inventory_id`, `created_at`.
10. THE System SHALL use an `activity_logs` table with columns: `id`, `user_id`, `action`, `created_at`.
