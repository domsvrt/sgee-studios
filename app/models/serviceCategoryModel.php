<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use PDOException;

class ServiceCategoryModel extends BaseModel
{
    private static ?bool $hasCategoryCodeColumn = null;

    public function all(): array
    {
        return $this->db->query('SELECT * FROM service_categories ORDER BY updated_at DESC, created_at DESC')->fetchAll();
    }

    public function create(array $data): void
    {
        $nextOrder = (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM service_categories')->fetchColumn();
        $data['sort_order'] = $nextOrder;
        if ($this->hasCategoryCodeColumn()) {
            $data['category_code'] = $this->nextCategoryCode();
            $stmt = $this->db->prepare(
                'INSERT INTO service_categories (category_code, name, description, is_active, sort_order)
                 VALUES (:category_code, :name, :description, :is_active, :sort_order)'
            );
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO service_categories (name, description, is_active, sort_order)
                 VALUES (:name, :description, :is_active, :sort_order)'
            );
        }
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

    private function hasCategoryCodeColumn(): bool
    {
        if (self::$hasCategoryCodeColumn !== null) {
            return self::$hasCategoryCodeColumn;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM service_categories LIKE 'category_code'");
        self::$hasCategoryCodeColumn = (bool) $stmt->fetch();
        return self::$hasCategoryCodeColumn;
    }

    private function nextCategoryCode(): string
    {
        $value = (int) $this->db->query(
            "SELECT COALESCE(MAX(CAST(SUBSTRING(category_code, 5) AS UNSIGNED)), 0) + 1
             FROM service_categories
             WHERE category_code REGEXP '^CAT-[0-9]+$'"
        )->fetchColumn();

        return 'CAT-' . str_pad((string) $value, 4, '0', STR_PAD_LEFT);
    }
}
