-- Add is_admin column to users table
ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0;

-- Update existing admin user
UPDATE users SET is_admin = 1 WHERE name = 'admin';

-- Add index for performance
CREATE INDEX idx_is_admin ON users(is_admin);

-- Update orders table to use product_id instead of id2
-- If you already have data, you'll need to migrate
-- For new installs, use this schema:

-- Create orders table with correct column names
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_email (email),
    INDEX idx_product (product_id)
);
