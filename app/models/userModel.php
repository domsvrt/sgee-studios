<?php

declare(strict_types=1);

namespace App\Models;

class UserModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public function bookers(): array
    {
        return $this->db->query("SELECT id, full_name, email FROM users WHERE role = 'user' AND status = 'active' ORDER BY full_name")->fetchAll();
    }

    public function adminCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (full_name, email, phone, role, admin_level, status, password_hash, visible_password)
             VALUES (:full_name, :email, :phone, :role, :admin_level, :status, :password_hash, :visible_password)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $passwordSql = isset($data['password_hash']) ? ', password_hash = :password_hash, visible_password = :visible_password' : '';
        $stmt = $this->db->prepare(
            "UPDATE users
             SET full_name = :full_name, email = :email, phone = :phone, role = :role,
                 admin_level = :admin_level, status = :status {$passwordSql}
             WHERE id = :id"
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function findActiveAdminByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND role = 'admin' AND status = 'active'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }
}
