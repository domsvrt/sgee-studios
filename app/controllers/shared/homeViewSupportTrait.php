<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

use App\Services\DiceBearService;

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
        $fullName = trim((string) ($_SESSION['user_full_name'] ?? ''));
        $email = trim((string) ($_SESSION['user_email'] ?? ''));
        $phone = trim((string) ($_SESSION['user_phone'] ?? ''));
        $avatarPath = trim((string) ($_SESSION['user_avatar'] ?? ''));

        if ($isUser && ($firstName === '' || $fullName === '' || $email === '' || $phone === '' || $avatarPath === '')) {
            $user = $this->users->find($userId);
            if ($user) {
                $firstName = $this->users->firstName($user);
                $lastName = trim((string) ($user['last_name'] ?? ''));
                $fullName = trim($firstName . ' ' . $lastName);
                $email = trim((string) ($user['email'] ?? ''));
                $phone = trim((string) ($user['phone'] ?? ''));
                $_SESSION['user_first_name'] = $firstName;
                $_SESSION['user_full_name'] = $fullName;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_phone'] = $phone;
                $_SESSION['user_avatar'] = $user['avatar_path'] ?? null;
                $avatarPath = trim((string) ($user['avatar_path'] ?? ''));
            }
        }

        return [
            'isLoggedIn' => $userId > 0,
            'isUser' => $isUser,
            'userFirstName' => $firstName ?: 'User',
            'userFullName' => $fullName ?: ($firstName ?: 'User'),
            'userEmail' => $email,
            'userPhone' => $phone,
            'userAvatarUrl' => $this->avatarPublicUrl($avatarPath, $fullName ?: $firstName, $email),
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

        $dir = __DIR__ . '/../../../storage/users';
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new \RuntimeException('Unable to create avatar upload directory.');
        }
        if (!is_writable($dir)) {
            @chmod($dir, 0775);
        }
        if (!is_writable($dir)) {
            throw new \RuntimeException('Avatar upload directory is not writable.');
        }

        $name = 'avatar_' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
        $target = $dir . '/' . $name;
        $moved = move_uploaded_file($tmp, $target);
        if (!$moved && is_uploaded_file($tmp)) {
            $moved = @copy($tmp, $target);
            if ($moved) {
                @unlink($tmp);
            }
        }
        if (!$moved) {
            throw new \RuntimeException('Unable to save avatar file. Please try again.');
        }

        return 'users/' . $name;
    }

    private function avatarPublicUrl(string $avatarPath, string $name = '', string $email = ''): string
    {
        $seed = trim($email) !== '' ? strtolower(trim($email)) : trim($name);
        if ($seed === '') {
            $seed = 'user';
        }
        $fallbackUrl = '/user-avatar?seed=' . rawurlencode($seed) . '&style=identicon';

        $avatarPath = trim($avatarPath);
        if ($avatarPath === '') {
            return $fallbackUrl;
        }

        if (str_starts_with($avatarPath, '/uploads/avatars/')) {
            $legacyFile = basename($avatarPath);
            $newPath = __DIR__ . '/../../../storage/users/' . $legacyFile;
            if (is_file($newPath)) {
                return '/user-avatar?file=' . rawurlencode($legacyFile);
            }
            return $fallbackUrl;
        }

        if (str_starts_with($avatarPath, '/')) {
            return $avatarPath;
        }

        if (str_starts_with($avatarPath, 'users/')) {
            $file = basename($avatarPath);
            $path = __DIR__ . '/../../../storage/users/' . $file;
            if (is_file($path)) {
                return '/user-avatar?file=' . rawurlencode($file);
            }
            return $fallbackUrl;
        }

        return $fallbackUrl;
    }
}
