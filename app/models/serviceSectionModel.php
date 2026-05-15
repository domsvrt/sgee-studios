<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;
use PDOException;

class ServiceSectionModel extends BaseModel
{
    private static ?bool $hasTable = null;
    private static ?bool $hasSectionCodeColumn = null;

    public function all(): array
    {
        if (!$this->hasTable()) {
            return [];
        }

        return $this->db->query(
            'SELECT service_sections.*, service_categories.name AS category_name
             FROM service_sections
             JOIN service_categories ON service_categories.id = service_sections.category_id
             ORDER BY service_sections.sort_order ASC, service_sections.updated_at DESC, service_sections.created_at DESC'
        )->fetchAll();
    }

    public function create(array $data): void
    {
        $this->assertTableExists();

        $nextOrder = (int) $this->db->query('SELECT COALESCE(MAX(sort_order), 0) + 1 FROM service_sections')->fetchColumn();
        $data['sort_order'] = $nextOrder;
        if ($this->hasSectionCodeColumn()) {
            $data['section_code'] = $this->nextSectionCode();
            $stmt = $this->db->prepare(
                'INSERT INTO service_sections (section_code, category_id, name, description, selection_type, is_active, sort_order)
                 VALUES (:section_code, :category_id, :name, :description, :selection_type, :is_active, :sort_order)'
            );
        } else {
            $stmt = $this->db->prepare(
                'INSERT INTO service_sections (category_id, name, description, selection_type, is_active, sort_order)
                 VALUES (:category_id, :name, :description, :selection_type, :is_active, :sort_order)'
            );
        }
        $stmt->execute($data);
    }

    public function update(int $id, array $data): void
    {
        $this->assertTableExists();

        $stmt = $this->db->prepare(
            'UPDATE service_sections
             SET category_id = :category_id, name = :name, description = :description,
                 selection_type = :selection_type, is_active = :is_active
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $this->assertTableExists();

        try {
            $stmt = $this->db->prepare('DELETE FROM service_sections WHERE id = :id');
            $stmt->execute(['id' => $id]);
        } catch (PDOException $exception) {
            if (($exception->errorInfo[1] ?? null) === 1451) {
                throw new InvalidArgumentException('Cannot delete this section because it is linked to services.');
            }
            throw $exception;
        }
    }

    public function reorder(array $ids): void
    {
        $this->assertTableExists();

        $stmt = $this->db->prepare('UPDATE service_sections SET sort_order = :sort_order WHERE id = :id');
        foreach (array_values($ids) as $index => $id) {
            $stmt->execute([
                'id' => (int) $id,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function hasTable(): bool
    {
        if (self::$hasTable !== null) {
            return self::$hasTable;
        }

        $stmt = $this->db->query("SHOW TABLES LIKE 'service_sections'");
        self::$hasTable = (bool) $stmt->fetchColumn();
        return self::$hasTable;
    }

    private function assertTableExists(): void
    {
        if (!$this->hasTable()) {
            throw new InvalidArgumentException('Service sections table is not available yet. Please apply migration 010_service_sections.sql.');
        }
    }

    private function hasSectionCodeColumn(): bool
    {
        if (self::$hasSectionCodeColumn !== null) {
            return self::$hasSectionCodeColumn;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM service_sections LIKE 'section_code'");
        self::$hasSectionCodeColumn = (bool) $stmt->fetchColumn();
        return self::$hasSectionCodeColumn;
    }

    private function nextSectionCode(): string
    {
        $value = (int) $this->db->query(
            "SELECT COALESCE(MAX(CAST(SUBSTRING(section_code, 5) AS UNSIGNED)), 0) + 1
             FROM service_sections
             WHERE section_code REGEXP '^SEC-[0-9]+$'"
        )->fetchColumn();

        return 'SEC-' . str_pad((string) $value, 4, '0', STR_PAD_LEFT);
    }
}
