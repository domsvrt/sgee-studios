<?php

declare(strict_types=1);

namespace App\Models\Shared;

use PDO;

class NotificationModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        $this->ensureTable();
    }

    public function recentForUser(int $userId, int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            'SELECT user_notifications.*, bookings.booking_code
             FROM user_notifications
             LEFT JOIN bookings ON bookings.id = user_notifications.booking_id
             WHERE user_notifications.user_id = :user_id
             ORDER BY user_notifications.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function unreadCountForUser(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM user_notifications WHERE user_id = :user_id AND is_read = 0');
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function createBookingStatusNotification(array $booking, string $newStatus): void
    {
        $label = $this->statusLabel($newStatus);
        $code = (string) ($booking['booking_code'] ?? 'your booking');
        $date = trim((string) ($booking['booking_date'] ?? '') . ' ' . substr((string) ($booking['booking_time'] ?? ''), 0, 5));

        $stmt = $this->db->prepare(
            'INSERT INTO user_notifications (user_id, booking_id, type, title, message)
             VALUES (:user_id, :booking_id, :type, :title, :message)'
        );
        $stmt->execute([
            'user_id' => (int) $booking['user_id'],
            'booking_id' => (int) $booking['id'],
            'type' => 'booking_status',
            'title' => "Booking {$code} {$label}",
            'message' => "Your booking for {$date} is now {$label}.",
        ]);
    }

    public function markRead(int $notificationId, int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE user_notifications
             SET is_read = 1, read_at = CURRENT_TIMESTAMP
             WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute(['id' => $notificationId, 'user_id' => $userId]);
    }

    public function markAllRead(int $userId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE user_notifications
             SET is_read = 1, read_at = CURRENT_TIMESTAMP
             WHERE user_id = :user_id AND is_read = 0'
        );
        $stmt->execute(['user_id' => $userId]);
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'confirmed' => 'Approved',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => ucfirst($status),
        };
    }

    private function ensureTable(): void
    {
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS user_notifications (
              id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              user_id BIGINT UNSIGNED NOT NULL,
              booking_id BIGINT UNSIGNED NULL,
              type VARCHAR(50) NOT NULL,
              title VARCHAR(150) NOT NULL,
              message VARCHAR(255) NOT NULL,
              is_read TINYINT(1) NOT NULL DEFAULT 0,
              read_at DATETIME NULL,
              created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              KEY idx_user_notifications_user_read_created (user_id, is_read, created_at),
              KEY idx_user_notifications_booking_id (booking_id),
              CONSTRAINT fk_user_notifications_user_id
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT fk_user_notifications_booking_id
                FOREIGN KEY (booking_id) REFERENCES bookings(id)
                ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );
    }
}
