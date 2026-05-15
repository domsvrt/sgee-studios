<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

class ActivityLogModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    public function create(string $type, string $title, string $message, ?int $userId = null, ?int $bookingId = null, ?array $metadata = null): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO activity_logs (type, title, message, user_id, booking_id, metadata_json)
             VALUES (:type, :title, :message, :user_id, :booking_id, :metadata_json)'
        );
        $stmt->execute([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'metadata_json' => $metadata ? json_encode($metadata, JSON_UNESCAPED_SLASHES) : null,
        ]);
    }

    public function recent(int $limit = 100): array
    {
        $nameSql = $this->hasUsersSplitNameColumns()
            ? "CONCAT(users.first_name, ' ', users.last_name)"
            : 'users.full_name';

        $stmt = $this->db->prepare(
            "SELECT activity_logs.*, bookings.booking_code, {$nameSql} AS user_name, users.email AS user_email
             FROM activity_logs
             LEFT JOIN users ON users.id = activity_logs.user_id
             LEFT JOIN bookings ON bookings.id = activity_logs.booking_id
             ORDER BY activity_logs.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function ensureTable(): void
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS activity_logs (
              id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              type VARCHAR(60) NOT NULL,
              title VARCHAR(150) NOT NULL,
              message VARCHAR(255) NOT NULL,
              user_id BIGINT UNSIGNED NULL,
              booking_id BIGINT UNSIGNED NULL,
              metadata_json JSON NULL,
              is_read TINYINT(1) NOT NULL DEFAULT 0,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              KEY idx_activity_logs_type_created (type, created_at),
              KEY idx_activity_logs_is_read_created (is_read, created_at),
              KEY idx_activity_logs_user_id (user_id),
              KEY idx_activity_logs_booking_id (booking_id),
              CONSTRAINT fk_activity_logs_user_id
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE SET NULL ON UPDATE CASCADE,
              CONSTRAINT fk_activity_logs_booking_id
                FOREIGN KEY (booking_id) REFERENCES bookings(id)
                ON DELETE SET NULL ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
