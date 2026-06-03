-- ============================================
-- SeedCycle Payment System Migration
-- ============================================

-- Add payment columns to orders table
ALTER TABLE orders
ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cod',
ADD COLUMN payment_status VARCHAR(20) DEFAULT 'pending',
ADD COLUMN payment_reference VARCHAR(255) DEFAULT NULL;

-- Update existing orders to have COD as default
UPDATE orders 
SET payment_method = 'cod', 
    payment_status = 'cod' 
WHERE payment_method IS NULL;

-- Create payment_transactions table for tracking payment history
CREATE TABLE IF NOT EXISTS payment_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(20) NOT NULL,
    payment_reference VARCHAR(255) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_data TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for faster queries
CREATE INDEX idx_payment_status ON orders(payment_status);
CREATE INDEX idx_payment_method ON orders(payment_method);
CREATE INDEX idx_payment_reference ON orders(payment_reference);
