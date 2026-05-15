<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

trait AdminPayloadTrait
{
    private function userPayload(?string $password): array
    {
        $role = $this->enum($_POST['role'] ?? 'user', ['user', 'manager', 'admin'], 'role');
        $payload = [
            'first_name' => $this->requiredString('first_name'),
            'last_name' => $this->requiredString('last_name'),
            'email' => $this->requiredString('email'),
            'phone' => trim($_POST['phone'] ?? '') ?: null,
            'role' => $role,
            'status' => $this->enum($_POST['status'] ?? 'active', ['active', 'inactive', 'banned'], 'status'),
        ];

        if ($password !== null) {
            $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        return $payload;
    }

    private function categoryPayload(): array
    {
        return [
            'name' => $this->requiredString('name'),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function servicePayload(): array
    {
        $price = (float) ($_POST['price'] ?? 0);
        if ($price < 0) {
            throw new \InvalidArgumentException('Price cannot be negative.');
        }

        $sectionIdRaw = trim((string) ($_POST['section_id'] ?? ''));

        return [
            'category_id' => (int) $this->requiredString('category_id'),
            'section_id' => $sectionIdRaw === '' ? null : (int) $sectionIdRaw,
            'code' => $this->slug($this->requiredString('code')),
            'name' => $this->requiredString('name'),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'price' => number_format($price, 2, '.', ''),
            'unit_label' => trim($_POST['unit_label'] ?? '') ?: null,
            'selection_type' => $this->enum($_POST['selection_type'] ?? 'multiple', ['single', 'multiple'], 'selection type'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function serviceSectionPayload(): array
    {
        return [
            'category_id' => (int) $this->requiredString('category_id'),
            'name' => $this->requiredString('name'),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'selection_type' => $this->enum($_POST['selection_type'] ?? 'multiple', ['single', 'multiple'], 'selection type'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }

    private function bookingPayload(): array
    {
        $code = trim($_POST['booking_code'] ?? '') ?: 'BK-' . strtoupper(bin2hex(random_bytes(4)));
        $categoryId = trim($_POST['category_id'] ?? '');

        return [
            'booking_code' => $code,
            'user_id' => (int) $this->requiredString('user_id'),
            'category_id' => $categoryId === '' ? null : (int) $categoryId,
            'booking_date' => $this->requiredString('booking_date'),
            'booking_time' => $this->requiredString('booking_time'),
            'status' => $this->enum($_POST['status'] ?? 'pending', ['pending', 'confirmed', 'completed', 'cancelled'], 'status'),
            'notes' => trim($_POST['notes'] ?? '') ?: null,
            'created_by_user_id' => null,
            'updated_by_user_id' => null,
        ];
    }

    private function requiredId(): int
    {
        return (int) $this->requiredString('id');
    }

    private function requiredString(string $key): string
    {
        $value = trim($_POST[$key] ?? '');

        if ($value === '') {
            throw new \InvalidArgumentException(str_replace('_', ' ', ucfirst($key)) . ' is required.');
        }

        return $value;
    }

    private function enum(string $value, array $allowed, string $label): string
    {
        if (!in_array($value, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid {$label}.");
        }

        return $value;
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9_-]+/', '-', $slug) ?: '';
        $slug = trim($slug, '-_');

        if ($slug === '') {
            throw new \InvalidArgumentException('Slug/code is required.');
        }

        return $slug;
    }
}
