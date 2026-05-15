<?php

declare(strict_types=1);

namespace App\Models;

class UserModel extends BaseModel
{
    private static ?bool $usersHasAvatarColumn = null;

    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public function bookers(): array
    {
        if ($this->hasUsersSplitNameColumns()) {
            return $this->db->query("SELECT id, first_name, last_name, CONCAT(first_name, ' ', last_name) AS full_name, email FROM users WHERE role = 'user' AND status = 'active' ORDER BY first_name, last_name")->fetchAll();
        }

        return $this->db->query("SELECT id, full_name, email FROM users WHERE role = 'user' AND status = 'active' ORDER BY full_name")->fetchAll();
    }

    public function adminCount(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM users WHERE role IN ('admin', 'manager')")->fetchColumn();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function firstName(array $user): string
    {
        $firstName = trim((string) ($user['first_name'] ?? ''));
        if ($firstName !== '') {
            return $firstName;
        }

        $fullName = trim((string) ($user['full_name'] ?? ''));
        if ($fullName !== '') {
            return (string) preg_split('/\s+/', $fullName, 2)[0];
        }

        return (string) ($user['email'] ?? 'User');
    }

    public function create(array $data): int
    {
        $hasAvatar = $this->hasAvatarColumn();

        if ($this->hasUsersSplitNameColumns()) {
            $stmt = $this->db->prepare(
                $hasAvatar
                    ? 'INSERT INTO users (first_name, last_name, email, phone, avatar_path, role, status, password_hash)
                       VALUES (:first_name, :last_name, :email, :phone, :avatar_path, :role, :status, :password_hash)'
                    : 'INSERT INTO users (first_name, last_name, email, phone, role, status, password_hash)
                       VALUES (:first_name, :last_name, :email, :phone, :role, :status, :password_hash)'
            );
            $params = [
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'] ?? 'user',
                'status' => $data['status'] ?? 'active',
                'password_hash' => $data['password_hash'] ?? '',
            ];
        } else {
            $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            $stmt = $this->db->prepare(
                $hasAvatar
                    ? 'INSERT INTO users (full_name, email, phone, avatar_path, role, status, password_hash)
                       VALUES (:full_name, :email, :phone, :avatar_path, :role, :status, :password_hash)'
                    : 'INSERT INTO users (full_name, email, phone, role, status, password_hash)
                       VALUES (:full_name, :email, :phone, :role, :status, :password_hash)'
            );
            $params = [
                'full_name' => $fullName,
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'] ?? 'user',
                'status' => $data['status'] ?? 'active',
                'password_hash' => $data['password_hash'] ?? '',
            ];
        }

        if ($hasAvatar) {
            $params['avatar_path'] = $data['avatar_path'] ?? null;
        }
        $stmt->execute($params);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $passwordSql = isset($data['password_hash']) ? ', password_hash = :password_hash' : '';
        $avatarSql = (array_key_exists('avatar_path', $data) && $this->hasAvatarColumn()) ? ', avatar_path = :avatar_path' : '';

        if ($this->hasUsersSplitNameColumns()) {
            $stmt = $this->db->prepare(
                "UPDATE users
                 SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone{$avatarSql}, role = :role,
                     status = :status {$passwordSql}
                 WHERE id = :id"
            );
            $params = [
                'first_name' => $data['first_name'] ?? '',
                'last_name' => $data['last_name'] ?? '',
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'] ?? 'user',
                'status' => $data['status'] ?? 'active',
                'id' => $id,
            ];
        } else {
            $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            $stmt = $this->db->prepare(
                "UPDATE users
                 SET full_name = :full_name, email = :email, phone = :phone{$avatarSql}, role = :role,
                     status = :status {$passwordSql}
                 WHERE id = :id"
            );
            $params = [
                'full_name' => $fullName,
                'email' => $data['email'] ?? '',
                'phone' => $data['phone'] ?? null,
                'role' => $data['role'] ?? 'user',
                'status' => $data['status'] ?? 'active',
                'id' => $id,
            ];
        }

        if (array_key_exists('avatar_path', $data)) {
            $params['avatar_path'] = $data['avatar_path'];
        }
        if (isset($data['password_hash'])) {
            $params['password_hash'] = $data['password_hash'];
        }

        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function findActiveAdminByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND role IN ('admin', 'manager') AND status = 'active'");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function updateAvatar(int $id, string $avatarPath): void
    {
        if (!$this->hasAvatarColumn()) {
            return;
        }

        $stmt = $this->db->prepare('UPDATE users SET avatar_path = :avatar_path WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'avatar_path' => $avatarPath,
        ]);
    }

    private function hasAvatarColumn(): bool
    {
        if (self::$usersHasAvatarColumn !== null) {
            return self::$usersHasAvatarColumn;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM users LIKE 'avatar_path'");
        self::$usersHasAvatarColumn = (bool) $stmt->fetch();
        return self::$usersHasAvatarColumn;
    }
}
