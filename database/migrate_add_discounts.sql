-- Adds Senior Citizen / PWD discount support to the sales table.
-- Run against an existing sarisari_pos database:
--   mysql -u root -p sarisari_pos < database/migrate_add_discounts.sql
-- (Already included in schema.sql for fresh installs.)

ALTER TABLE sales
    ADD COLUMN subtotal_amount DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER reference_no,
    ADD COLUMN discount_type ENUM('none', 'senior', 'pwd') NOT NULL DEFAULT 'none' AFTER subtotal_amount,
    ADD COLUMN discount_id_number VARCHAR(50) NULL AFTER discount_type,
    ADD COLUMN discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER discount_id_number;

UPDATE sales SET subtotal_amount = total_amount WHERE subtotal_amount = 0;
