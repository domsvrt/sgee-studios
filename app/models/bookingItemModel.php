<?php

declare(strict_types=1);

namespace App\Models;

class BookingItemModel extends BaseModel
{
    public function groupedByBooking(): array
    {
        $rows = $this->db->query('SELECT * FROM booking_items ORDER BY created_at')->fetchAll();
        return $this->groupRows($rows);
    }

    public function groupedByBookingIds(array $bookingIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $bookingIds)));
        if (!$ids) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("SELECT * FROM booking_items WHERE booking_id IN ({$placeholders}) ORDER BY created_at");
        $stmt->execute($ids);
        return $this->groupRows($stmt->fetchAll());
    }

    private function groupRows(array $rows): array
    {
        $grouped = [];

        foreach ($rows as $row) {
            $grouped[(int) $row['booking_id']][] = $row;
        }

        return $grouped;
    }

    public function replaceForBooking(int $bookingId, array $serviceIds): void
    {
        $this->db->prepare('DELETE FROM booking_items WHERE booking_id = :booking_id')->execute(['booking_id' => $bookingId]);
        $serviceModel = new ServiceModel();

        foreach ($serviceIds as $serviceId) {
            $service = $serviceModel->find((int) $serviceId);
            if (!$service) {
                continue;
            }

            $stmt = $this->db->prepare(
                'INSERT INTO booking_items (booking_id, service_id, service_name_snapshot, unit_price_snapshot, quantity, line_total)
                 VALUES (:booking_id, :service_id, :name, :price, 1, :total)'
            );
            $stmt->execute([
                'booking_id' => $bookingId,
                'service_id' => (int) $service['id'],
                'name' => $service['name'],
                'price' => $service['price'],
                'total' => $service['price'],
            ]);
        }
    }
}
