<?php

declare(strict_types=1);

namespace App\Models\Shared;

class UserModel extends BaseModel
{
    private static ?bool $usersHasAvatarColumn = null;
    private static ?bool $usersHasUserCodeColumn = null;

    public function all(): array
    {
        return $this->db->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public function bookers(): array
    {
        return $this->db->query("SELECT id, first_name, last_name, CONCAT(first_name, ' ', last_name) AS full_name, email FROM users WHERE role = 'user' AND status = 'active' ORDER BY first_name, last_name")->fetchAll();
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

        return (string) ($user['email'] ?? 'User');
    }

    public function create(array $data): int
    {
        $hasAvatar = $this->hasAvatarColumn();
        $hasUserCode = $this->hasUserCodeColumn();
        $userCode = $hasUserCode ? $this->nextUserCode() : null;

        $stmt = $this->db->prepare(
            $hasAvatar
                ? ($hasUserCode
                    ? 'INSERT INTO users (user_code, first_name, last_name, email, phone, avatar_path, role, status, password_hash)
                       VALUES (:user_code, :first_name, :last_name, :email, :phone, :avatar_path, :role, :status, :password_hash)'
                    : 'INSERT INTO users (first_name, last_name, email, phone, avatar_path, role, status, password_hash)
                       VALUES (:first_name, :last_name, :email, :phone, :avatar_path, :role, :status, :password_hash)')
                : ($hasUserCode
                    ? 'INSERT INTO users (user_code, first_name, last_name, email, phone, role, status, password_hash)
                       VALUES (:user_code, :first_name, :last_name, :email, :phone, :role, :status, :password_hash)'
                    : 'INSERT INTO users (first_name, last_name, email, phone, role, status, password_hash)
                       VALUES (:first_name, :last_name, :email, :phone, :role, :status, :password_hash)')
        );

        $params = [
            'first_name' => trim((string) ($data['first_name'] ?? '')),
            'last_name' => trim((string) ($data['last_name'] ?? '')),
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user',
            'status' => $data['status'] ?? 'active',
            'password_hash' => $data['password_hash'] ?? '',
        ];

        if ($hasAvatar) {
            $params['avatar_path'] = $data['avatar_path'] ?? null;
        }
        if ($hasUserCode) {
            $params['user_code'] = $userCode;
        }

        $stmt->execute($params);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $passwordSql = isset($data['password_hash']) ? ', password_hash = :password_hash' : '';
        $avatarSql = (array_key_exists('avatar_path', $data) && $this->hasAvatarColumn()) ? ', avatar_path = :avatar_path' : '';

        $stmt = $this->db->prepare(
            "UPDATE users
             SET first_name = :first_name, last_name = :last_name, email = :email, phone = :phone{$avatarSql}, role = :role,
                 status = :status {$passwordSql}
             WHERE id = :id"
        );

        $params = [
            'first_name' => trim((string) ($data['first_name'] ?? '')),
            'last_name' => trim((string) ($data['last_name'] ?? '')),
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'] ?? 'user',
            'status' => $data['status'] ?? 'active',
            'id' => $id,
        ];

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

    public function updateProfile(int $id, string $firstName, string $lastName, string $email): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET first_name = :first_name, last_name = :last_name, email = :email
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
        ]);
    }

    public function updatePassword(int $id, string $passwordHash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'password_hash' => $passwordHash,
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

    private function hasUserCodeColumn(): bool
    {
        if (self::$usersHasUserCodeColumn !== null) {
            return self::$usersHasUserCodeColumn;
        }

        $stmt = $this->db->query("SHOW COLUMNS FROM users LIKE 'user_code'");
        self::$usersHasUserCodeColumn = (bool) $stmt->fetch();
        return self::$usersHasUserCodeColumn;
    }

    private function nextUserCode(): string
    {
        $value = (int) $this->db->query(
            "SELECT COALESCE(MAX(CAST(SUBSTRING(user_code, 5) AS UNSIGNED)), 0) + 1
             FROM users
             WHERE user_code REGEXP '^USR-[0-9]+$'"
        )->fetchColumn();

        return 'USR-' . str_pad((string) $value, 6, '0', STR_PAD_LEFT);
    }
}
