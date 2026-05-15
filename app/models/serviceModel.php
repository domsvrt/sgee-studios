<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use PDOException;

class ServiceModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT services.*, service_categories.name AS category_name
             FROM services
             JOIN service_categories ON service_categories.id = services.category_id
             ORDER BY services.updated_at DESC, services.created_at DESC'
        )->fetchAll();
    }

    public function activeCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn();
    }

    public function create(array $data): void
    {
        $nextOrder = (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM services')->fetchColumn();
        $data['sort_order'] = $nextOrder;
        $stmt = $this->db->prepare(
            'INSERT INTO services (category_id, code, name, description, price, unit_label, selection_type, is_active, sort_order)
             VALUES (:category_id, :code, :name, :description, :price, :unit_label, :selection_type, :is_active, :sort_order)'
        );
        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE services
             SET category_id = :category_id, code = :code, name = :name, description = :description,
                 price = :price, unit_label = :unit_label, selection_type = :selection_type,
                 is_active = :is_active
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM services WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451) {
                throw new InvalidArgumentException('Cannot delete this service because it is already used in bookings. Set it to inactive instead.');
            }
            throw $exception;
        }
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM services WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $service = $stmt->fetch();
        return $service ?: null;
    }

    public function reorder(array $ids): void
    {
        $stmt = $this->db->prepare('UPDATE services SET sort_order = :sort_order WHERE id = :id');
        foreach (array_values($ids) as $index => $id) {
            $stmt->execute([
                'id' => (int) $id,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
