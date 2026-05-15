<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

trait HomeViewSupportTrait
{
    private function renderHome(array $data): void
    {
        $auth = $this->authViewData();
        if (!array_key_exists('flash', $data)) {
            $data['flash'] = $this->pullFlash();
        }
        $page = (string) ($data['page'] ?? 'home');
        $userPages = ['my-bookings', 'notifications', 'settings'];
        $viewPath = in_array($page, $userPages, true) ? 'user/home.php' : 'public/home.php';
        $this->render($viewPath, array_merge($data, $auth));
    }

    private function authViewData(): array
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['user_role'] ?? null;
        $isUser = $userId > 0 && $role === 'user';
        $firstName = trim((string) ($_SESSION['user_first_name'] ?? ''));
        $avatarPath = trim((string) ($_SESSION['user_avatar'] ?? ''));

        if ($isUser && ($firstName === '' || $avatarPath === '')) {
            $user = $this->users->find($userId);
            if ($user) {
                $firstName = $this->users->firstName($user);
                $_SESSION['user_first_name'] = $firstName;
                $_SESSION['user_avatar'] = $user['avatar_path'] ?? null;
                $avatarPath = trim((string) ($user['avatar_path'] ?? ''));
            }
        }

        return [
            'isLoggedIn' => $userId > 0,
            'isUser' => $isUser,
            'userFirstName' => $firstName ?: 'User',
            'userAvatarUrl' => $this->avatarPublicUrl($avatarPath),
            'notificationUnreadCount' => $isUser ? $this->notifications->unreadCountForUser($userId) : 0,
            'recentNotifications' => $isUser ? $this->notifications->recentForUser($userId, 5) : [],
        ];
    }

    private function requireUser(): int
    {
        $this->requireRole(['user']);
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    private function storeAvatarUpload(?array $file): string
    {
        if (!$file || !isset($file['error'])) {
            throw new \InvalidArgumentException('Avatar file is required.');
        }
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new \InvalidArgumentException('Avatar upload failed.');
        }
        if ((int) ($file['size'] ?? 0) > 2 * 1024 * 1024) {
            throw new \InvalidArgumentException('Avatar must be 2MB or smaller.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $mime = mime_content_type($tmp) ?: '';
        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($allowed[$mime])) {
            throw new \InvalidArgumentException('Only JPG, PNG, or WEBP avatars are allowed.');
        }

        $dir = __DIR__ . '/../../storage/users';
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create avatar upload directory.');
        }
        if (!is_writable($dir)) {
            throw new \RuntimeException('Avatar upload directory is not writable.');
        }

        $name = 'avatar_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $target = $dir . '/' . $name;
        if (!move_uploaded_file($tmp, $target)) {
            throw new \RuntimeException('Unable to save avatar file.');
        }

        return 'users/' . $name;
    }

    private function avatarPublicUrl(string $avatarPath): string
    {
        $avatarPath = trim($avatarPath);
        if ($avatarPath === '') {
            return '';
        }

        if (str_starts_with($avatarPath, '/uploads/avatars/')) {
            $legacyFile = basename($avatarPath);
            $newPath = __DIR__ . '/../../storage/users/' . $legacyFile;
            if (is_file($newPath)) {
                return '/user-avatar?file=' . rawurlencode($legacyFile);
            }
            return $avatarPath;
        }

        if (str_starts_with($avatarPath, '/')) {
            return $avatarPath;
        }

        if (str_starts_with($avatarPath, 'users/')) {
            return '/user-avatar?file=' . rawurlencode(basename($avatarPath));
        }

        return '';
    }
}
