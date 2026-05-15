<?php

declare(strict_types=1);

namespace App\Models;

class BookingModel extends BaseModel
{
    public function all(): array
    {
        $userNameSql = "CONCAT(users.first_name, ' ', users.last_name)";

        return $this->db->query(
            "SELECT bookings.*, {$userNameSql} AS user_name, service_categories.name AS category_name
             FROM bookings
             JOIN users ON users.id = bookings.user_id
             LEFT JOIN service_categories ON service_categories.id = bookings.category_id
             ORDER BY bookings.updated_at DESC, bookings.created_at DESC"
        )->fetchAll();
    }

    public function upcoming(): array
    {
        $userNameSql = "CONCAT(users.first_name, ' ', users.last_name)";

        return $this->db->query(
            "SELECT bookings.*, {$userNameSql} AS user_name
             FROM bookings
             JOIN users ON users.id = bookings.user_id
             WHERE bookings.booking_date >= CURDATE()
             ORDER BY bookings.booking_date, bookings.booking_time
             LIMIT 8"
        )->fetchAll();
    }

    public function forUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT bookings.*, service_categories.name AS category_name
             FROM bookings
             LEFT JOIN service_categories ON service_categories.id = bookings.category_id
             WHERE bookings.user_id = :user_id
             ORDER BY bookings.updated_at DESC, bookings.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function statusCounts(): array
    {
        return $this->db->query('SELECT status, COUNT(*) AS total FROM bookings GROUP BY status')->fetchAll();
    }

    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bookings WHERE status = :status');
        $stmt->execute(['status' => $status]);
        return (int) $stmt->fetchColumn();
    }

    public function upcomingCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM bookings WHERE booking_date >= CURDATE()")->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bookings (booking_code, user_id, category_id, booking_date, booking_time, status, notes, total_amount, created_by_user_id, updated_by_user_id)
             VALUES (:booking_code, :user_id, :category_id, :booking_date, :booking_time, :status, :notes, 0.00, :created_by_user_id, :updated_by_user_id)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE bookings
             SET booking_code = :booking_code, user_id = :user_id, category_id = :category_id,
                 booking_date = :booking_date, booking_time = :booking_time, status = :status,
                 notes = :notes, updated_by_user_id = :updated_by_user_id
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function updateStatus(int $id, string $status, ?int $changedByUserId): ?array
    {
        $booking = $this->find($id);
        if (!$booking) {
            return null;
        }

        $stmt = $this->db->prepare('UPDATE bookings SET status = :status, updated_by_user_id = :user_id WHERE id = :id');
        $stmt->execute(['status' => $status, 'user_id' => $changedByUserId, 'id' => $id]);
        return $booking;
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM bookings WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM bookings WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $booking = $stmt->fetch();
        return $booking ?: null;
    }

    public function recalculateTotal(int $id): void
    {
        $stmt = $this->db->prepare(
            'UPDATE bookings
             SET total_amount = COALESCE((SELECT SUM(line_total) FROM booking_items WHERE booking_id = :id), 0)
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }

}
