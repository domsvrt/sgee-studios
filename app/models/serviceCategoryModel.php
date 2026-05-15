<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use PDOException;

class ServiceCategoryModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM service_categories ORDER BY updated_at DESC, created_at DESC')->fetchAll();
    }

    public function create(array $data): void
    {
        $nextOrder = (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM service_categories')->fetchColumn();
        $data['sort_order'] = $nextOrder;
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
             SET name = :name, description = :description, is_active = :is_active
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM service_categories WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451) {
                throw new InvalidArgumentException('Cannot delete this category because it is linked to services or bookings. Set it to inactive instead.');
            }
            throw $exception;
        }
    }

    public function reorder(array $ids): void
    {
        $stmt = $this->db->prepare('UPDATE service_categories SET sort_order = :sort_order WHERE id = :id');
        foreach (array_values($ids) as $index => $id) {
            $stmt->execute([
                'id' => (int) $id,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
