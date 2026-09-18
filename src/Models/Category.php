<?php

declare(strict_types=1);

namespace App\Models;

class Category extends Model
{
    protected string $table = 'categories';

    public const COLORS = ['blue', 'orange', 'purple', 'yellow', 'teal', 'pink'];

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name, color) VALUES (:name, :color)');
        $stmt->execute(['name' => $data['name'], 'color' => $data['color'] ?: null]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = :name, color = :color WHERE id = :id');

        return $stmt->execute(['name' => $data['name'], 'color' => $data['color'] ?: null, 'id' => $id]);
    }
}
