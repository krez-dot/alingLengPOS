-- Backfills 6 days of sample sales (before today) so dashboard charts and
-- reports have something to show. Safe to run once on a freshly seeded
-- database; re-running will insert duplicates since reference_no values
-- are only unique per run, not idempotent.
--   mysql -u root -p sarisari_pos < database/seed_demo_sales.sql

INSERT INTO sales (reference_no, subtotal_amount, discount_type, discount_amount, total_amount, amount_paid, change_due, created_at) VALUES
('TXN-20260912103000-101', 155.00, 'none', 0, 155.00, 155.00, 0.00, '2026-09-12 10:30:00'),
('TXN-20260912153000-102', 83.00, 'none', 0, 83.00, 100.00, 17.00, '2026-09-12 15:30:00'),
('TXN-20260913110000-103', 60.00, 'none', 0, 60.00, 60.00, 0.00, '2026-09-13 11:00:00'),
('TXN-20260913140000-104', 85.00, 'none', 0, 85.00, 100.00, 15.00, '2026-09-13 14:00:00'),
('TXN-20260913170000-105', 38.00, 'none', 0, 38.00, 40.00, 2.00, '2026-09-13 17:00:00'),
('TXN-20260914100000-106', 120.00, 'none', 0, 120.00, 120.00, 0.00, '2026-09-14 10:00:00'),
('TXN-20260914160000-107', 35.00, 'none', 0, 35.00, 40.00, 5.00, '2026-09-14 16:00:00'),
('TXN-20260915093000-108', 160.00, 'none', 0, 160.00, 200.00, 40.00, '2026-09-15 09:30:00'),
('TXN-20260915133000-109', 76.00, 'none', 0, 76.00, 80.00, 4.00, '2026-09-15 13:30:00'),
('TXN-20260915183000-110', 60.00, 'none', 0, 60.00, 60.00, 0.00, '2026-09-15 18:30:00'),
('TXN-20260916114500-111', 110.00, 'none', 0, 110.00, 110.00, 0.00, '2026-09-16 11:45:00'),
('TXN-20260916154500-112', 50.00, 'none', 0, 50.00, 50.00, 0.00, '2026-09-16 15:45:00'),
('TXN-20260917102000-113', 135.00, 'none', 0, 135.00, 135.00, 0.00, '2026-09-17 10:20:00'),
('TXN-20260917152000-114', 114.00, 'none', 0, 114.00, 120.00, 6.00, '2026-09-17 15:20:00'),
('TXN-20260917192000-115', 30.00, 'none', 0, 30.00, 30.00, 0.00, '2026-09-17 19:20:00');

-- Sale items (product_id references the original seed: 1=Coke, 2=Bottled Water,
-- 4=Piattos, 5=Century Tuna, 6=Safeguard Soap, 7=Surf Powder — product 3
-- (Pancit Canton) is skipped since it's already low-stock)
INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal)
SELECT s.id, v.product_id, v.quantity, v.unit_price, v.subtotal
FROM sales s
JOIN (
    SELECT 'TXN-20260912103000-101' ref, 1 product_id, 2 quantity, 65.00 unit_price, 130.00 subtotal UNION ALL
    SELECT 'TXN-20260912103000-101', 6, 1, 25.00, 25.00 UNION ALL
    SELECT 'TXN-20260912153000-102', 2, 3, 15.00, 45.00 UNION ALL
    SELECT 'TXN-20260912153000-102', 5, 1, 38.00, 38.00 UNION ALL
    SELECT 'TXN-20260913110000-103', 4, 1, 30.00, 30.00 UNION ALL
    SELECT 'TXN-20260913110000-103', 2, 2, 15.00, 30.00 UNION ALL
    SELECT 'TXN-20260913140000-104', 7, 2, 10.00, 20.00 UNION ALL
    SELECT 'TXN-20260913140000-104', 1, 1, 65.00, 65.00 UNION ALL
    SELECT 'TXN-20260913170000-105', 5, 1, 38.00, 38.00 UNION ALL
    SELECT 'TXN-20260914100000-106', 2, 4, 15.00, 60.00 UNION ALL
    SELECT 'TXN-20260914100000-106', 4, 2, 30.00, 60.00 UNION ALL
    SELECT 'TXN-20260914160000-107', 6, 1, 25.00, 25.00 UNION ALL
    SELECT 'TXN-20260914160000-107', 7, 1, 10.00, 10.00 UNION ALL
    SELECT 'TXN-20260915093000-108', 1, 2, 65.00, 130.00 UNION ALL
    SELECT 'TXN-20260915093000-108', 7, 3, 10.00, 30.00 UNION ALL
    SELECT 'TXN-20260915133000-109', 5, 2, 38.00, 76.00 UNION ALL
    SELECT 'TXN-20260915183000-110', 4, 1, 30.00, 30.00 UNION ALL
    SELECT 'TXN-20260915183000-110', 2, 2, 15.00, 30.00 UNION ALL
    SELECT 'TXN-20260916114500-111', 2, 3, 15.00, 45.00 UNION ALL
    SELECT 'TXN-20260916114500-111', 1, 1, 65.00, 65.00 UNION ALL
    SELECT 'TXN-20260916154500-112', 6, 2, 25.00, 50.00 UNION ALL
    SELECT 'TXN-20260917102000-113', 1, 1, 65.00, 65.00 UNION ALL
    SELECT 'TXN-20260917102000-113', 4, 2, 30.00, 60.00 UNION ALL
    SELECT 'TXN-20260917102000-113', 7, 1, 10.00, 10.00 UNION ALL
    SELECT 'TXN-20260917152000-114', 5, 3, 38.00, 114.00 UNION ALL
    SELECT 'TXN-20260917192000-115', 2, 2, 15.00, 30.00
) v ON v.ref = s.reference_no;

-- Deduct the seeded quantities from stock so levels stay consistent
UPDATE products SET stock_quantity = stock_quantity - 7 WHERE id = 1;  -- Coke
UPDATE products SET stock_quantity = stock_quantity - 16 WHERE id = 2; -- Bottled Water
UPDATE products SET stock_quantity = stock_quantity - 6 WHERE id = 4;  -- Piattos
UPDATE products SET stock_quantity = stock_quantity - 7 WHERE id = 5;  -- Century Tuna
UPDATE products SET stock_quantity = stock_quantity - 4 WHERE id = 6;  -- Safeguard Soap
UPDATE products SET stock_quantity = stock_quantity - 7 WHERE id = 7;  -- Surf Powder

-- Matching stock movement log entries
INSERT INTO stock_movements (product_id, movement_type, quantity, reason, created_at)
SELECT v.product_id, 'out', v.quantity, CONCAT('Sale #', v.ref), s.created_at
FROM sales s
JOIN (
    SELECT 'TXN-20260912103000-101' ref, 1 product_id, 2 quantity UNION ALL
    SELECT 'TXN-20260912103000-101', 6, 1 UNION ALL
    SELECT 'TXN-20260912153000-102', 2, 3 UNION ALL
    SELECT 'TXN-20260912153000-102', 5, 1 UNION ALL
    SELECT 'TXN-20260913110000-103', 4, 1 UNION ALL
    SELECT 'TXN-20260913110000-103', 2, 2 UNION ALL
    SELECT 'TXN-20260913140000-104', 7, 2 UNION ALL
    SELECT 'TXN-20260913140000-104', 1, 1 UNION ALL
    SELECT 'TXN-20260913170000-105', 5, 1 UNION ALL
    SELECT 'TXN-20260914100000-106', 2, 4 UNION ALL
    SELECT 'TXN-20260914100000-106', 4, 2 UNION ALL
    SELECT 'TXN-20260914160000-107', 6, 1 UNION ALL
    SELECT 'TXN-20260914160000-107', 7, 1 UNION ALL
    SELECT 'TXN-20260915093000-108', 1, 2 UNION ALL
    SELECT 'TXN-20260915093000-108', 7, 3 UNION ALL
    SELECT 'TXN-20260915133000-109', 5, 2 UNION ALL
    SELECT 'TXN-20260915183000-110', 4, 1 UNION ALL
    SELECT 'TXN-20260915183000-110', 2, 2 UNION ALL
    SELECT 'TXN-20260916114500-111', 2, 3 UNION ALL
    SELECT 'TXN-20260916114500-111', 1, 1 UNION ALL
    SELECT 'TXN-20260916154500-112', 6, 2 UNION ALL
    SELECT 'TXN-20260917102000-113', 1, 1 UNION ALL
    SELECT 'TXN-20260917102000-113', 4, 2 UNION ALL
    SELECT 'TXN-20260917102000-113', 7, 1 UNION ALL
    SELECT 'TXN-20260917152000-114', 5, 3 UNION ALL
    SELECT 'TXN-20260917192000-115', 2, 2
) v ON v.ref = s.reference_no;
