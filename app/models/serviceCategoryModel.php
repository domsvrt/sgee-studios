<?php

declare(strict_types=1);

namespace App\Models;

class ServiceCategoryModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM service_categories ORDER BY sort_order, name')->fetchAll();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO service_categories (name, description, is_active, sort_order)
             VALUES (:name, :description, :is_active, :sort_order)'
        );
        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE service_categories
             SET name = :name, description = :description, is_active = :is_active, sort_order = :sort_order
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM service_categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
