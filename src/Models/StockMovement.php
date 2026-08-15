<?php

declare(strict_types=1);

namespace App\Models;

class StockMovement extends Model
{
    protected string $table = 'stock_movements';

    public function create(array $data): int
    {
        return $this->log(
            (int) $data['product_id'],
            $data['movement_type'],
            (int) $data['quantity'],
            $data['reason'] ?? ''
        );
    }

    public function log(int $productId, string $type, int $quantity, string $reason = ''): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO stock_movements (product_id, movement_type, quantity, reason)
             VALUES (:product_id, :type, :quantity, :reason)'
        );
        $stmt->execute([
            'product_id' => $productId,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $reason,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        return false;
    }

    public function forProduct(int $productId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM stock_movements WHERE product_id = :id ORDER BY created_at DESC');
        $stmt->execute(['id' => $productId]);

        return $stmt->fetchAll();
    }
}
