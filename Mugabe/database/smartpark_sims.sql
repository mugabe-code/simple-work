-- SmartPark Stock Inventory Management System (SIMS)
-- Database Schema

-- Create database
CREATE DATABASE IF NOT EXISTS smartpark_sims;
USE smartpark_sims;

-- Users table
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    names VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Spare Parts table
CREATE TABLE spare_parts (
    part_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    quantity INT DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL
);

-- Stock In table
CREATE TABLE stock_in (
    stock_in_id INT PRIMARY KEY AUTO_INCREMENT,
    part_id INT NOT NULL,
    quantity INT NOT NULL,
    stock_in_date DATE NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (part_id) REFERENCES spare_parts(part_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Stock Out table
CREATE TABLE stock_out (
    stock_out_id INT PRIMARY KEY AUTO_INCREMENT,
    part_id INT NOT NULL,
    quantity INT NOT NULL,
    stock_out_unit_price DECIMAL(10,2) NOT NULL,
    stock_out_total_price DECIMAL(10,2) NOT NULL,
    stock_out_date DATE NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (part_id) REFERENCES spare_parts(part_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Insert sample users
INSERT INTO users (names, username, password) VALUES 
('Admin User', 'admin', 'admin123'),
('Stock Manager', 'manager', 'manager123');

-- Insert sample spare parts
INSERT INTO spare_parts (name, category, quantity, unit_price, total_price) VALUES 
('Engine Oil Filter', 'Engine Parts', 50, 15.50, 775.00),
('Brake Pad Set', 'Brake System', 30, 45.00, 1350.00),
('Air Filter', 'Engine Parts', 40, 12.00, 480.00),
('Spark Plug', 'Ignition System', 100, 8.75, 875.00),
('Battery', 'Electrical System', 15, 120.00, 1800.00);

-- Insert sample stock in records
INSERT INTO stock_in (part_id, quantity, stock_in_date, user_id) VALUES 
(1, 20, '2026-02-25', 1),
(2, 15, '2026-02-24', 2),
(3, 25, '2026-02-23', 1);

-- Insert sample stock out records
INSERT INTO stock_out (part_id, quantity, stock_out_unit_price, stock_out_total_price, stock_out_date, user_id) VALUES 
(1, 5, 15.50, 77.50, '2026-02-26', 2),
(4, 10, 8.75, 87.50, '2026-02-26', 1),
(2, 3, 45.00, 135.00, '2026-02-25', 2);