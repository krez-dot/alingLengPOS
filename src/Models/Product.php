<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\InsufficientStockException;
use PDO;

class Product extends Model
{
    protected string $table = 'products';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products (sku, name, category_id, supplier_id, cost_price, selling_price, stock_quantity, reorder_level)
             VALUES (:sku, :name, :category_id, :supplier_id, :cost_price, :selling_price, :stock_quantity, :reorder_level)'
        );
        $stmt->execute([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'] ?: null,
            'cost_price' => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'stock_quantity' => $data['stock_quantity'],
            'reorder_level' => $data['reorder_level'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE products SET sku = :sku, name = :name, category_id = :category_id, supplier_id = :supplier_id,
             cost_price = :cost_price, selling_price = :selling_price, stock_quantity = :stock_quantity,
             reorder_level = :reorder_level WHERE id = :id'
        );

        return $stmt->execute([
            'sku' => $data['sku'],
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'] ?: null,
            'cost_price' => $data['cost_price'],
            'selling_price' => $data['selling_price'],
            'stock_quantity' => $data['stock_quantity'],
            'reorder_level' => $data['reorder_level'],
            'id' => $id,
        ]);
    }

    public function search(
        string $keyword = '',
        ?int $categoryId = null,
        string $sortBy = 'name',
        string $direction = 'ASC',
        int $limit = 20,
        int $offset = 0
    ): array {
        $allowedSort = ['name', 'selling_price', 'stock_quantity', 'created_at'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'name';
        }
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        $sql = 'SELECT p.*, c.name AS category_name, c.color AS category_color, s.name AS supplier_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN suppliers s ON p.supplier_id = s.id
                WHERE p.name LIKE :keyword';
        $params = ['keyword' => "%{$keyword}%"];

        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $sql .= " ORDER BY p.{$sortBy} {$direction} LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countSearch(string $keyword = '', ?int $categoryId = null): int
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE name LIKE :keyword';
        $params = ['keyword' => "%{$keyword}%"];

        if ($categoryId !== null) {
            $sql .= ' AND category_id = :category_id';
            $params['category_id'] = $categoryId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function lowStock(): array
    {
        return $this->db
            ->query('SELECT * FROM products WHERE stock_quantity <= reorder_level ORDER BY stock_quantity ASC')
            ->fetchAll();
    }

    public function deductStock(int $productId, int $quantity): void
    {
        $stmt = $this->db->prepare('SELECT stock_quantity FROM products WHERE id = :id FOR UPDATE');
        $stmt->execute(['id' => $productId]);
        $current = $stmt->fetchColumn();

        if ($current === false) {
            throw new InsufficientStockException("Product #{$productId} not found.");
        }

        if ((int) $current < $quantity) {
            throw new InsufficientStockException(
                "Insufficient stock for product #{$productId}. Available: {$current}, requested: {$quantity}."
            );
        }

        $update = $this->db->prepare('UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :id');
        $update->execute(['qty' => $quantity, 'id' => $productId]);
    }

    public function restock(int $productId, int $quantity): void
    {
        $update = $this->db->prepare('UPDATE products SET stock_quantity = stock_quantity + :qty WHERE id = :id');
        $update->execute(['qty' => $quantity, 'id' => $productId]);
    }
}
