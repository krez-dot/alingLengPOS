-- Sari-Sari Store POS Database Schema
-- Run this file to create the database, tables, and seed sample data:
--   mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS sarisari_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sarisari_pos;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(30),
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT NULL,
    cost_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    selling_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    stock_quantity INT NOT NULL DEFAULT 0,
    reorder_level INT NOT NULL DEFAULT 10,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    movement_type ENUM('in', 'out') NOT NULL,
    quantity INT NOT NULL,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reference_no VARCHAR(30) NOT NULL UNIQUE,
    subtotal_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount_type ENUM('none', 'senior', 'pwd') NOT NULL DEFAULT 'none',
    discount_id_number VARCHAR(50) NULL,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0,
    change_due DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Seed data
INSERT INTO categories (name) VALUES
('Beverages'), ('Snacks'), ('Canned Goods'), ('Personal Care'), ('Household');

INSERT INTO suppliers (name, contact_person, phone, address) VALUES
('Coca-Cola Bottlers Phils.', 'Juan Dela Cruz', '0917-123-4567', 'Tarlac City'),
('Monde Nissin Corp.', 'Maria Santos', '0918-234-5678', 'San Isidro, Tarlac'),
('Unilever Philippines', 'Pedro Reyes', '0919-345-6789', 'Tarlac City');

INSERT INTO products (sku, name, category_id, supplier_id, cost_price, selling_price, stock_quantity, reorder_level) VALUES
('BEV-001', 'Coke 1.5L', 1, 1, 45.00, 65.00, 24, 10),
('BEV-002', 'Bottled Water 500mL', 1, 1, 8.00, 15.00, 50, 15),
('SNK-001', 'Lucky Me Pancit Canton', 2, 2, 10.00, 15.00, 8, 20),
('SNK-002', 'Piattos Cheese 85g', 2, 2, 22.00, 30.00, 30, 10),
('CAN-001', 'Century Tuna 155g', 3, NULL, 28.00, 38.00, 40, 12),
('PC-001', 'Safeguard Soap 90g', 4, 3, 18.00, 25.00, 20, 10),
('HH-001', 'Surf Powder 66g', 5, 3, 6.00, 10.00, 60, 20);
