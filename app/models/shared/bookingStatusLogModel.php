<?php

declare(strict_types=1);

namespace App\Models\Shared;

use PDO;

class BookingStatusLogModel extends BaseModel
{
    public function recent(int $limit = 10): array
    {
        $userNameSql = "CONCAT(users.first_name, ' ', users.last_name)";

        $stmt = $this->db->prepare(
            "SELECT booking_status_logs.*, bookings.booking_code, {$userNameSql} AS changed_by_name
             FROM booking_status_logs
             JOIN bookings ON bookings.id = booking_status_logs.booking_id
             LEFT JOIN users ON users.id = booking_status_logs.changed_by_user_id
             ORDER BY booking_status_logs.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(int $bookingId, ?string $oldStatus, string $newStatus, ?int $changedByUserId, ?string $note): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO booking_status_logs (booking_id, old_status, new_status, changed_by_user_id, change_note)
             VALUES (:booking_id, :old_status, :new_status, :changed_by_user_id, :change_note)'
        );
        $stmt->execute([
            'booking_id' => $bookingId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by_user_id' => $changedByUserId,
            'change_note' => $note,
        ]);
    }
}
