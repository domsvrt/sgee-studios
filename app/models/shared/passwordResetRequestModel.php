<?php

declare(strict_types=1);

namespace App\Models\Shared;

use PDO;

class PasswordResetRequestModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    public function createRequest(?int $userId, string $email): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO password_reset_requests (user_id, email_snapshot, status)
             VALUES (:user_id, :email_snapshot, :status)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'email_snapshot' => $email,
            'status' => 'pending',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function all(?string $status = null, int $limit = 200): array
    {
        $nameSql = "CONCAT(u.first_name, ' ', u.last_name)";

        $resolverNameSql = "CONCAT(r.first_name, ' ', r.last_name)";

        $sql = "SELECT prr.*, {$nameSql} AS requester_name, u.email AS requester_email, {$resolverNameSql} AS resolver_name
                FROM password_reset_requests prr
                LEFT JOIN users u ON u.id = prr.user_id
                LEFT JOIN users r ON r.id = prr.resolved_by_user_id";

        $params = [];
        if ($status !== null && $status !== '') {
            $sql .= ' WHERE prr.status = :status';
            $params['status'] = $status;
        }

        $sql .= ' ORDER BY prr.requested_at DESC LIMIT :limit';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM password_reset_requests WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status, int $resolvedByUserId, ?string $notes = null): ?array
    {
        $existing = $this->find($id);
        if (!$existing) {
            return null;
        }

        $stmt = $this->db->prepare(
            'UPDATE password_reset_requests
             SET status = :status,
                 resolved_by_user_id = :resolved_by_user_id,
                 resolved_at = CASE WHEN :status = "pending" THEN NULL ELSE CURRENT_TIMESTAMP END,
                 notes = :notes
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'status' => $status,
            'resolved_by_user_id' => $resolvedByUserId,
            'notes' => $notes,
        ]);

        return $existing;
    }

    private function ensureTable(): void
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS password_reset_requests (
              id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              user_id BIGINT UNSIGNED NULL,
              email_snapshot VARCHAR(191) NOT NULL,
              status ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
              requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              resolved_at DATETIME NULL,
              resolved_by_user_id BIGINT UNSIGNED NULL,
              notes VARCHAR(255) NULL,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              KEY idx_password_reset_requests_status_requested (status, requested_at),
              KEY idx_password_reset_requests_user_id (user_id),
              KEY idx_password_reset_requests_resolved_by (resolved_by_user_id),
              CONSTRAINT fk_password_reset_requests_user_id
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT fk_password_reset_requests_resolved_by
                FOREIGN KEY (resolved_by_user_id) REFERENCES users(id)
                ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
