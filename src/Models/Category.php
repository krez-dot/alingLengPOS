<?php

declare(strict_types=1);

namespace App\Models;

class Category extends Model
{
    protected string $table = 'categories';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO categories (name) VALUES (:name)');
        $stmt->execute(['name' => $data['name']]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET name = :name WHERE id = :id');

        return $stmt->execute(['name' => $data['name'], 'id' => $id]);
    }
}
