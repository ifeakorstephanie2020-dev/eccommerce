-- Create database
CREATE DATABASE IF NOT EXISTS adaez;
USE adaez;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users table (for customer signup)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    id2 INT NOT NULL,  -- This references product ID
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id2) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample products (optional)
INSERT INTO products (name, description, price) VALUES
('Product 1', 'Description for product 1', 19.99),
('Product 2', 'Description for product 2', 29.99),
('Product 3', 'Description for product 3', 39.99);

-- Insert default admin user (optional - for admin login)
-- Note: In production, you should use hashed passwords!
INSERT INTO users (name, pass) VALUES ('admin', 'admin123');
