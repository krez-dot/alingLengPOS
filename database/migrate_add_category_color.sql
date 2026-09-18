-- Adds a user-selectable badge color to categories (falls back to the
-- automatic hash-based color when NULL).
--   mysql -u root -p sarisari_pos < database/migrate_add_category_color.sql
-- (Already included in schema.sql for fresh installs.)

ALTER TABLE categories
    ADD COLUMN color ENUM('blue', 'orange', 'purple', 'yellow', 'teal', 'pink') NULL AFTER name;

UPDATE categories SET color = 'blue' WHERE name = 'Beverages';
UPDATE categories SET color = 'yellow' WHERE name = 'Snacks';
UPDATE categories SET color = 'teal' WHERE name = 'Canned Goods';
UPDATE categories SET color = 'purple' WHERE name = 'Personal Care';
UPDATE categories SET color = 'orange' WHERE name = 'Household';
