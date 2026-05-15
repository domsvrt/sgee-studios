<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use PDOException;

class ServiceModel extends BaseModel
{
    private static ?bool $hasServiceSectionsTable = null;
    private static ?bool $hasServicesSectionIdColumn = null;

    public function all(): array
    {
        if (!$this->hasServiceSectionsTable() || !$this->hasServicesSectionIdColumn()) {
            return $this->db->query(
                'SELECT services.*, service_categories.name AS category_name
                 FROM services
                 JOIN service_categories ON service_categories.id = services.category_id
                 ORDER BY services.updated_at DESC, services.created_at DESC'
            )->fetchAll();
        }

        return $this->db->query(
            'SELECT services.*, service_categories.name AS category_name, service_sections.name AS section_name, service_sections.selection_type AS section_selection_type
             FROM services
             JOIN service_categories ON service_categories.id = services.category_id
             LEFT JOIN service_sections ON service_sections.id = services.section_id
             ORDER BY services.updated_at DESC, services.created_at DESC'
        )->fetchAll();
    }

    public function activeCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM services WHERE is_active = 1')->fetchColumn();
    }

    public function activeCatalogRows(): array
    {
        if (!$this->hasServiceSectionsTable() || !$this->hasServicesSectionIdColumn()) {
            return $this->db->query(
                'SELECT services.*, service_categories.name AS category_name
                 FROM services
                 JOIN service_categories ON service_categories.id = services.category_id
                 WHERE services.is_active = 1 AND service_categories.is_active = 1
                 ORDER BY service_categories.sort_order ASC, services.sort_order ASC, services.id ASC'
            )->fetchAll();
        }

        return $this->db->query(
            "SELECT
                services.*,
                service_categories.name AS category_name,
                service_sections.id AS section_id,
                service_sections.name AS section_name,
                service_sections.description AS section_description,
                service_sections.selection_type AS section_selection_type,
                service_sections.sort_order AS section_sort_order
             FROM services
             JOIN service_categories ON service_categories.id = services.category_id
             LEFT JOIN service_sections ON service_sections.id = services.section_id
             WHERE services.is_active = 1
               AND service_categories.is_active = 1
               AND (service_sections.id IS NULL OR service_sections.is_active = 1)
             ORDER BY service_categories.sort_order ASC, service_sections.sort_order ASC, services.sort_order ASC, services.id ASC"
        )->fetchAll();
    }

    public function create(array $data): void
    {
        $nextOrder = (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM services')->fetchColumn();
        $data['sort_order'] = $nextOrder;
        if ($this->hasServicesSectionIdColumn()) {
            $stmt = $this->db->prepare(
                'INSERT INTO services (category_id, section_id, code, name, description, price, unit_label, selection_type, is_active, sort_order)
                 VALUES (:category_id, :section_id, :code, :name, :description, :price, :unit_label, :selection_type, :is_active, :sort_order)'
            );
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO services (category_id, code, name, description, price, unit_label, selection_type, is_active, sort_order)
                 VALUES (:category_id, :code, :name, :description, :price, :unit_label, :selection_type, :is_active, :sort_order)'
            );
            unset($data['section_id']);
        }
        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        if ($this->hasServicesSectionIdColumn()) {
            $stmt = $this->db->prepare(
                'UPDATE services
                 SET category_id = :category_id, section_id = :section_id, code = :code, name = :name, description = :description,
                     price = :price, unit_label = :unit_label, selection_type = :selection_type,
                     is_active = :is_active
                 WHERE id = :id'
            );
        } else {
            $stmt = $this->db->prepare(
                'UPDATE services
                 SET category_id = :category_id, code = :code, name = :name, description = :description,
                     price = :price, unit_label = :unit_label, selection_type = :selection_type,
                     is_active = :is_active
                 WHERE id = :id'
            );
            unset($data['section_id']);
        }
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

    private function hasServiceSectionsTable(): bool
    {
        if (self::$hasServiceSectionsTable !== null) {
            return self::$hasServiceSectionsTable;
        }

        $stmt = $this->db->query("SHOW TABLES LIKE 'service_sections'");
        self::$hasServiceSectionsTable = (bool) $stmt->fetchColumn();
        return self::$hasServiceSectionsTable;
    }

    private function hasServicesSectionIdColumn(): bool
    {
        if (self::$hasServicesSectionIdColumn !== null) {
            return self::$hasServicesSectionIdColumn;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM services LIKE 'section_id'");
        self::$hasServicesSectionIdColumn = (bool) $stmt->fetchColumn();
        return self::$hasServicesSectionIdColumn;
    }
}
