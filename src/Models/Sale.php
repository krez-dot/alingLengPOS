<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use Throwable;

class Sale extends Model
{
    protected string $table = 'sales';

    /**
     * Creates a sale transaction with its line items in a single atomic operation:
     * stock is deducted per item, and the whole sale is rolled back if any item
     * fails (e.g. insufficient stock), so partial/oversold transactions can't occur.
     */
    public function create(array $data): int
    {
        $product = new Product();
        $stockMovement = new StockMovement();

        $this->db->beginTransaction();

        try {
            $total = 0.0;
            foreach ($data['items'] as $item) {
                $total += $item['unit_price'] * $item['quantity'];
            }

            $stmt = $this->db->prepare(
                'INSERT INTO sales (reference_no, total_amount, amount_paid, change_due)
                 VALUES (:reference_no, :total_amount, :amount_paid, :change_due)'
            );
            $stmt->execute([
                'reference_no' => $data['reference_no'],
                'total_amount' => $total,
                'amount_paid' => $data['amount_paid'],
                'change_due' => $data['amount_paid'] - $total,
            ]);
            $saleId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal)
                 VALUES (:sale_id, :product_id, :quantity, :unit_price, :subtotal)'
            );

            foreach ($data['items'] as $item) {
                $product->deductStock((int) $item['product_id'], (int) $item['quantity']);

                $itemStmt->execute([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['unit_price'] * $item['quantity'],
                ]);

                $stockMovement->log(
                    (int) $item['product_id'],
                    'out',
                    (int) $item['quantity'],
                    "Sale #{$data['reference_no']}"
                );
            }

            $this->db->commit();

            return $saleId;
        } catch (Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): bool
    {
        return false;
    }

    public function history(
        string $keyword = '',
        string $dateFrom = '',
        string $dateTo = '',
        string $order = 'DESC',
        int $limit = 20,
        int $offset = 0
    ): array {
        $sql = 'SELECT * FROM sales WHERE 1=1';
        $params = [];

        if ($keyword !== '') {
            $sql .= ' AND reference_no LIKE :keyword';
            $params['keyword'] = "%{$keyword}%";
        }

        if ($dateFrom !== '') {
            $sql .= ' AND created_at >= :date_from';
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }

        if ($dateTo !== '') {
            $sql .= ' AND created_at <= :date_to';
            $params['date_to'] = $dateTo . ' 23:59:59';
        }

        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY created_at {$order} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function itemsFor(int $saleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT si.*, p.name AS product_name FROM sale_items si
             JOIN products p ON si.product_id = p.id WHERE si.sale_id = :sale_id'
        );
        $stmt->execute(['sale_id' => $saleId]);

        return $stmt->fetchAll();
    }

    public function todayTotal(): float
    {
        return (float) $this->db
            ->query('SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE DATE(created_at) = CURDATE()')
            ->fetchColumn();
    }

    public function todayCount(): int
    {
        return (int) $this->db
            ->query('SELECT COUNT(*) FROM sales WHERE DATE(created_at) = CURDATE()')
            ->fetchColumn();
    }

    public function topSelling(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.id, p.name, SUM(si.quantity) AS total_qty
             FROM sale_items si
             JOIN products p ON p.id = si.product_id
             GROUP BY p.id, p.name
             ORDER BY total_qty DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
