CREATE DATABASE IF NOT EXISTS smartpark_sims;
USE smartpark_sims;

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    names VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE spare_parts (
    part_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    quantity INT DEFAULT 0,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL
);

CREATE TABLE stock_in (
    stock_in_id INT PRIMARY KEY AUTO_INCREMENT,
    part_id INT NOT NULL,
    quantity INT NOT NULL,
    stock_in_date DATE NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (part_id) REFERENCES spare_parts(part_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

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

INSERT INTO users (names, username, password) VALUES 
('Admin User', 'admin', 'admin123'),
('Stock Manager', 'manager', 'manager123');

INSERT INTO spare_parts (name, category, quantity, unit_price, total_price) VALUES 
('Engine Oil Filter', 'Engine Parts', 50, 15.50, 775.00),
('Brake Pad Set', 'Brake System', 30, 45.00, 1350.00),
('Air Filter', 'Engine Parts', 40, 12.00, 480.00),
('Spark Plug', 'Ignition System', 100, 8.75, 875.00),
('Battery', 'Electrical System', 15, 120.00, 1800.00);

INSERT INTO stock_in (part_id, quantity, stock_in_date, user_id) VALUES 
(1, 20, '2026-02-25', 1),
(2, 15, '2026-02-24', 2),
(3, 25, '2026-02-23', 1);

INSERT INTO stock_out (part_id, quantity, stock_out_unit_price, stock_out_total_price, stock_out_date, user_id) VALUES 
(1, 5, 15.50, 77.50, '2026-02-26', 2),
(4, 10, 8.75, 87.50, '2026-02-26', 1),
(2, 3, 45.00, 135.00, '2026-02-25', 2);

SELECT COUNT(*) as total_users FROM users;
SELECT COUNT(*) as total_parts FROM spare_parts;
SELECT COUNT(*) as total_stock_in FROM stock_in;
SELECT COUNT(*) as total_stock_out FROM stock_out;

SELECT * FROM users;
SELECT * FROM spare_parts;
SELECT * FROM stock_in;
SELECT * FROM stock_out;

SELECT sp.name, sp.category, sp.quantity, sp.unit_price, sp.total_price 
FROM spare_parts sp 
ORDER BY sp.category, sp.name;

SELECT si.*, sp.name as part_name, u.names as user_name 
FROM stock_in si 
JOIN spare_parts sp ON si.part_id = sp.part_id 
JOIN users u ON si.user_id = u.user_id 
ORDER BY si.stock_in_date DESC;

SELECT so.*, sp.name as part_name, u.names as user_name 
FROM stock_out so 
JOIN spare_parts sp ON so.part_id = sp.part_id 
JOIN users u ON so.user_id = u.user_id 
ORDER BY so.stock_out_date DESC;

SELECT 
    COUNT(*) as total_items,
    SUM(total_price) as total_value,
    SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
    SUM(CASE WHEN quantity < 10 THEN 1 ELSE 0 END) as low_stock
FROM spare_parts;

SELECT 
    category,
    COUNT(*) as item_count,
    SUM(quantity) as total_quantity,
    SUM(total_price) as category_value
FROM spare_parts
GROUP BY category
ORDER BY category;

SELECT 
    DATE_FORMAT(stock_in_date, '%Y-%m') as month,
    COUNT(*) as transactions,
    SUM(quantity) as total_quantity
FROM stock_in
GROUP BY DATE_FORMAT(stock_in_date, '%Y-%m')
ORDER BY month DESC;

SELECT 
    DATE_FORMAT(stock_out_date, '%Y-%m') as month,
    COUNT(*) as transactions,
    SUM(quantity) as total_quantity,
    SUM(stock_out_total_price) as total_value
FROM stock_out
GROUP BY DATE_FORMAT(stock_out_date, '%Y-%m')
ORDER BY month DESC;