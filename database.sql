-- Create database
CREATE DATABASE IF NOT EXISTS adaez;
USE adaez;

-- Products table (unchanged)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users table with password hashing
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL,  -- Store hashed passwords
    email VARCHAR(255),          -- Added for order confirmation
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_username (name)
);

-- Orders table with better structure
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    order_status VARCHAR(50) DEFAULT 'pending',
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_email (email),
    INDEX idx_product (product_id)
);

-- Insert sample products (optional)
INSERT INTO products (name, description, price) VALUES
('Product 1', 'Description for product 1', 19.99),
('Product 2', 'Description for product 2', 29.99),
('Product 3', 'Description for product 3', 39.99);

-- Insert default admin with hashed password (password: admin123)
-- Use PHP's password_hash() to generate this
INSERT INTO users (name, pass, email) VALUES ('admin', '$2y$10$YourHashedPasswordHere', 'admin@example.com');
