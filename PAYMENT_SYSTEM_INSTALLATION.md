# SeedCycle Payment System Installation Guide

## Overview
This payment system adds Cash on Delivery (COD) and simulated GCash payment functionality to SeedCycle.

## Installation Steps

### 1. Database Migration
Run the SQL migration file to add payment columns and tables:

```bash
mysql -u your_username -p your_database < seed_cycle_payment_system.sql
```

Or manually execute the SQL in phpMyAdmin/MySQL Workbench.

### 2. Verify File Structure
Ensure all files are in place:

```
SeedCycle/
├── app/
│   ├── Controllers/
│   │   ├── OrderController.php (updated)
│   │   └── PaymentController.php (new)
│   ├── Models/
│   │   └── (existing models)
│   └── Views/
│       ├── checkout.php (updated)
│       ├── orders.php (updated)
│       ├── payment-gcash.php (new)
│       ├── payment-success.php (new)
│       └── payment-failed.php (new)
├── public/
│   ├── assets/css/
│   │   ├── checkout.css (updated)
│   │   └── orders.css (updated)
│   ├── payment-gcash.php (new)
│   ├── payment-process.php (new)
│   ├── payment-success.php (new)
│   └── payment-failed.php (new)
└── seed_cycle_payment_system.sql (new)
```

### 3. Test the System

#### Test COD Payment:
1. Add seeds to cart
2. Go to checkout
3. Fill in shipping address
4. Select "Cash on Delivery"
5. Click "Place Order"
6. Should redirect to success page with COD status

#### Test GCash Payment:
1. Add seeds to cart
2. Go to checkout
3. Fill in shipping address
4. Select "GCash"
5. Click "Place Order"
6. Should redirect to GCash payment page
7. Click "Pay Now"
8. Wait for processing (1.5 seconds)
9. 95% chance: Success page with payment reference
10. 5% chance: Failed page with retry option

### 4. Verify Database Updates

Check that orders have payment information:

```sql
SELECT id, user_id, total_amount, payment_method, payment_status, payment_reference 
FROM orders 
ORDER BY created_at DESC 
LIMIT 10;
```

Check payment transactions:

```sql
SELECT * FROM payment_transactions ORDER BY created_at DESC LIMIT 10;
```

## Features

### Payment Methods
- **Cash on Delivery (COD)**: Immediate order creation, pay on delivery
- **GCash**: Simulated online payment with reference number

### Payment Statuses
- `pending`: Payment not yet completed (GCash orders awaiting payment)
- `paid`: Payment successfully completed (GCash)
- `cod`: Cash on Delivery (payment on delivery)
- `failed`: Payment attempt failed

### User Experience
1. **Checkout Page**: Select payment method before placing order
2. **GCash Payment Page**: Simulated payment interface with processing animation
3. **Success Page**: Order confirmation with payment details
4. **Failed Page**: Error message with retry option
5. **Orders Page**: Payment status badges and "Pay Now" button for pending payments

### Payment Flow

#### COD Flow:
```
Cart → Checkout → Select COD → Place Order → Success Page → Orders
```

#### GCash Flow:
```
Cart → Checkout → Select GCash → Place Order → GCash Payment Page → 
Process Payment → Success/Failed Page → Orders
```

## Customization

### Adjust Success Rate
Edit `app/Controllers/PaymentController.php`:

```php
// Line ~60: Change success rate (default 95%)
$success = (rand(1, 100) <= 95); // Change 95 to desired percentage
```

### Add More Payment Methods
1. Add option in `app/Views/checkout.php`
2. Update `OrderController::checkout()` validation
3. Add processing logic in `PaymentController`
4. Create payment page view if needed

### Customize Payment Reference Format
Edit `PaymentController::generatePaymentReference()`:

```php
public static function generatePaymentReference(string $method = 'gcash'): string {
    // Customize format here
    $prefix = strtoupper(substr($method, 0, 3));
    $timestamp = date('YmdHis');
    $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
    return "{$prefix}-{$timestamp}-{$random}";
}
```

## Troubleshooting

### Orders not showing payment status
- Run the database migration
- Clear browser cache
- Check that `payment_method` and `payment_status` columns exist in orders table

### GCash payment not processing
- Check browser console for JavaScript errors
- Verify `payment-process.php` is accessible
- Check PHP error logs

### Payment reference not generating
- Ensure `PaymentController` is properly loaded
- Check that `payment_reference` column exists in orders table

## Security Notes

⚠️ **This is a SIMULATED payment system for development/demo purposes**

For production use:
1. Integrate real payment gateway (PayMongo, PayPal, Stripe, etc.)
2. Add proper payment verification
3. Implement webhook handlers for payment notifications
4. Add SSL/TLS encryption
5. Implement proper error handling and logging
6. Add payment reconciliation system
7. Implement refund functionality

## Support

For issues or questions:
- Check the code comments in `PaymentController.php`
- Review the database schema in `seed_cycle_payment_system.sql`
- Test with different scenarios (success, failure, COD)

## License

Part of the SeedCycle project.
