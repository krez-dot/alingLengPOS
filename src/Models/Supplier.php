<?php

declare(strict_types=1);

namespace App\Models;

class Supplier extends Model
{
    protected string $table = 'suppliers';

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO suppliers (name, contact_person, phone, address) VALUES (:name, :contact_person, :phone, :address)'
        );
        $stmt->execute([
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE suppliers SET name = :name, contact_person = :contact_person, phone = :phone, address = :address WHERE id = :id'
        );

        return $stmt->execute([
            'name' => $data['name'],
            'contact_person' => $data['contact_person'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'id' => $id,
        ]);
    }
}
