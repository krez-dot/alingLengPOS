-- Wipes ALL data (products, categories, suppliers, sales, sale_items,
-- stock_movements) and reseeds the database back to its original clean
-- state. Auto-increment counters are reset too (TRUNCATE, not DELETE).
--
-- THIS IS DESTRUCTIVE AND CANNOT BE UNDONE. Run only when you actually
-- want to wipe everything back to a fresh install.
--   mysql -u root -p sarisari_pos < database/reset.sql

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE sale_items;
TRUNCATE TABLE stock_movements;
TRUNCATE TABLE sales;
TRUNCATE TABLE products;
TRUNCATE TABLE suppliers;
TRUNCATE TABLE categories;
SET FOREIGN_KEY_CHECKS = 1;

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
