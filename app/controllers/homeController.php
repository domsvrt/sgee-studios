<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BookingItemModel;
use App\Models\BookingModel;
use App\Models\ActivityLogModel;
use App\Models\NotificationModel;
use App\Models\PasswordResetRequestModel;
use App\Models\ServiceCategoryModel;
use App\Models\ServiceModel;
use App\Models\ServiceSectionModel;
use App\Models\UserModel;

class HomeController extends BaseController
{
    private UserModel $users;
    private ServiceCategoryModel $categories;
    private ServiceSectionModel $sections;
    private ServiceModel $services;
    private BookingModel $bookings;
    private BookingItemModel $bookingItems;
    private NotificationModel $notifications;
    private PasswordResetRequestModel $passwordResetRequests;
    private ActivityLogModel $activityLogs;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->categories = new ServiceCategoryModel();
        $this->sections = new ServiceSectionModel();
        $this->services = new ServiceModel();
        $this->bookings = new BookingModel();
        $this->bookingItems = new BookingItemModel();
        $this->notifications = new NotificationModel();
        $this->passwordResetRequests = new PasswordResetRequestModel();
        $this->activityLogs = new ActivityLogModel();
    }

    public function index(): void
    {
        $categories = array_values(array_filter(
            $this->categories->all(),
            static fn (array $category): bool => (int) ($category['is_active'] ?? 0) === 1
        ));
        $this->renderHome(['page' => 'home', 'categories' => $categories]);
    }

    public function about(): void
    {
        $this->renderHome(['page' => 'about']);
    }

    public function contact(): void
    {
        $this->renderHome(['page' => 'contact']);
    }

    public function bookNow(): void
    {
        $categories = array_values(array_filter(
            $this->categories->all(),
            static fn (array $category): bool => (int) ($category['is_active'] ?? 0) === 1
        ));
        usort($categories, static fn (array $a, array $b): int => ((int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0)));

        $sectionRows = $this->sections->all();
        $serviceRows = $this->services->activeCatalogRows();

        $sectionsByCategory = [];
        foreach ($sectionRows as $section) {
            if ((int) ($section['is_active'] ?? 0) !== 1) {
                continue;
            }

            $categoryId = (int) ($section['category_id'] ?? 0);
            $sectionsByCategory[$categoryId][] = [
                'id' => (int) $section['id'],
                'name' => (string) ($section['name'] ?? ''),
                'description' => (string) ($section['description'] ?? ''),
                'selection_type' => (string) ($section['selection_type'] ?? 'multiple'),
                'sort_order' => (int) ($section['sort_order'] ?? 0),
                'items' => [],
            ];
        }

        foreach ($serviceRows as $service) {
            $categoryId = (int) ($service['category_id'] ?? 0);
            if (!isset($sectionsByCategory[$categoryId])) {
                $sectionsByCategory[$categoryId] = [];
            }

            $sectionId = (int) ($service['section_id'] ?? 0);
            $targetIndex = null;
            foreach ($sectionsByCategory[$categoryId] as $index => $section) {
                if ((int) $section['id'] === $sectionId) {
                    $targetIndex = $index;
                    break;
                }
            }
            if ($targetIndex === null) {
                $sectionsByCategory[$categoryId][] = [
                    'id' => $sectionId,
                    'name' => (string) ($service['section_name'] ?? 'Services'),
                    'description' => (string) ($service['section_description'] ?? ''),
                    'selection_type' => (string) ($service['section_selection_type'] ?? 'multiple'),
                    'sort_order' => (int) ($service['section_sort_order'] ?? 9999),
                    'items' => [],
                ];
                $targetIndex = array_key_last($sectionsByCategory[$categoryId]);
            }

            $sectionsByCategory[$categoryId][$targetIndex]['items'][] = [
                'id' => (int) $service['id'],
                'code' => (string) ($service['code'] ?? ''),
                'name' => (string) ($service['name'] ?? ''),
                'description' => (string) ($service['description'] ?? ''),
                'price' => (float) ($service['price'] ?? 0),
                'unit_label' => (string) ($service['unit_label'] ?? ''),
                'selection_type' => (string) ($service['selection_type'] ?? 'multiple'),
            ];
        }

        $catalog = [];
        foreach ($categories as $category) {
            $categoryId = (int) ($category['id'] ?? 0);
            $sections = $sectionsByCategory[$categoryId] ?? [];
            usort($sections, static fn (array $a, array $b): int => ((int) ($a['sort_order'] ?? 0) <=> (int) ($b['sort_order'] ?? 0)));

            $sections = array_values(array_filter($sections, static fn (array $section): bool => !empty($section['items'])));
            $catalog[] = [
                'id' => $categoryId,
                'name' => (string) ($category['name'] ?? ''),
                'description' => (string) ($category['description'] ?? ''),
                'sections' => $sections,
            ];
        }

        $this->renderHome([
            'page' => 'book-now',
            'bookNowCatalog' => $catalog,
        ]);
    }

    public function signIn(): void
    {
        $this->renderHome(['page' => 'sign-in', 'flash' => $this->pullFlash()]);
    }

    public function signUp(): void
    {
        $this->renderHome(['page' => 'sign-up', 'flash' => $this->pullFlash()]);
    }

    public function forgotPassword(): void
    {
        $this->renderHome(['page' => 'forgot-password', 'flash' => $this->pullFlash()]);
    }

    public function doSignIn(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash']) || ($user['status'] ?? '') !== 'active') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid credentials.'];
            $this->redirect('/sign-in');
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['user_first_name'] = $this->users->firstName($user);
        $_SESSION['user_avatar'] = $user['avatar_path'] ?? null;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signed in successfully.'];
        $isAdminUser = in_array($_SESSION['user_role'], ['admin', 'manager'], true);
        $this->redirect($isAdminUser ? '/admin' : '/');
    }

    public function doSignUp(): void
    {
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($firstName === '' || $lastName === '' || $phone === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'First name, last name, phone number, email, and password are required.'];
            $this->redirect('/sign-up');
        }

        if ($this->users->findByEmail($email)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email is already registered.'];
            $this->redirect('/sign-up');
        }

        $id = $this->users->create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'avatar_path' => null,
            'role' => 'user',
            'status' => 'active',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created. Please sign in.'];
        $this->redirect('/sign-in');
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_first_name'], $_SESSION['user_avatar']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signed out.'];
        $this->redirect('/sign-in');
    }

    public function requestPasswordReset(): void
    {
        $email = strtolower(trim($_POST['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Please enter a valid email address.'];
            $this->redirect('/forgot-password');
        }

        $user = $this->users->findByEmail($email);
        $userId = $user ? (int) $user['id'] : null;
        $requestId = $this->passwordResetRequests->createRequest($userId, $email);

        $this->activityLogs->create(
            'password_reset_request',
            'Password reset requested',
            "A forgot-password request was submitted for {$email}.",
            $userId,
            null,
            ['request_id' => $requestId]
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'If the account exists, your request has been sent to support.'];
        $this->redirect('/sign-in');
    }

    public function uploadAvatar(): void
    {
        $userId = $this->requireUser();

        try {
            $avatarPath = $this->storeAvatarUpload($_FILES['avatar'] ?? null);
            $this->users->updateAvatar($userId, $avatarPath);
            $_SESSION['user_avatar'] = $avatarPath;
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile picture updated.'];
        } catch (\Throwable $exception) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $exception->getMessage()];
        }

        $this->redirect($_POST['redirect'] ?? '/');
    }

    public function myBookings(): void
    {
        $userId = $this->requireUser();
        $bookings = $this->bookings->forUser($userId);
        $bookingItems = $this->bookingItems->groupedByBookingIds(array_column($bookings, 'id'));

        $this->renderHome([
            'page' => 'my-bookings',
            'bookings' => $bookings,
            'bookingItems' => $bookingItems,
            'flash' => $this->pullFlash(),
        ]);
    }

    public function notifications(): void
    {
        $userId = $this->requireUser();
        $this->renderHome([
            'page' => 'notifications',
            'notificationsPage' => $this->notifications->recentForUser($userId, 50),
            'flash' => $this->pullFlash(),
        ]);
    }

    public function settings(): void
    {
        $userId = $this->requireUser();
        $user = $this->users->find($userId);
        if (!$user) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Unable to load account settings.'];
            $this->redirect('/');
        }

        $firstName = trim((string) ($user['first_name'] ?? ''));
        $lastName = trim((string) ($user['last_name'] ?? ''));

        $this->renderHome([
            'page' => 'settings',
            'settingsUser' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => (string) ($user['email'] ?? ''),
            ],
            'flash' => $this->pullFlash(),
        ]);
    }

    public function updateProfileSettings(): void
    {
        $userId = $this->requireUser();
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));

        if ($firstName === '' || $lastName === '' || $email === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'First name, last name, and email are required.'];
            $this->redirect('/settings');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Please enter a valid email address.'];
            $this->redirect('/settings');
        }

        $existing = $this->users->findByEmail($email);
        if ($existing && (int) $existing['id'] !== $userId) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email is already in use by another account.'];
            $this->redirect('/settings');
        }

        $this->users->updateProfile($userId, $firstName, $lastName, $email);
        $_SESSION['user_first_name'] = $firstName;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile settings updated.'];
        $this->redirect('/settings');
    }

    public function updatePasswordSettings(): void
    {
        $userId = $this->requireUser();
        $currentPassword = trim($_POST['current_password'] ?? '');
        $newPassword = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'All password fields are required.'];
            $this->redirect('/settings');
        }

        if (strlen($newPassword) < 8) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'New password must be at least 8 characters.'];
            $this->redirect('/settings');
        }

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Password confirmation does not match.'];
            $this->redirect('/settings');
        }

        $user = $this->users->find($userId);
        if (!$user || !password_verify($currentPassword, (string) $user['password_hash'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Current password is incorrect.'];
            $this->redirect('/settings');
        }

        $this->users->updatePassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Password updated successfully.'];
        $this->redirect('/settings');
    }

    public function markNotificationRead(): void
    {
        $userId = $this->requireUser();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->notifications->markRead($id, $userId);
        }
        $this->redirect($_POST['redirect'] ?? '/notifications');
    }

    public function markAllNotificationsRead(): void
    {
        $userId = $this->requireUser();
        $this->notifications->markAllRead($userId);
        $this->redirect($_POST['redirect'] ?? '/notifications');
    }

    public function userAvatar(): void
    {
        $file = basename((string) ($_GET['file'] ?? ''));
        if ($file === '') {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $path = __DIR__ . '/../../storage/users/' . $file;
        if (!is_file($path)) {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $mime = mime_content_type($path) ?: 'application/octet-stream';
        header('Content-Type: ' . $mime);
        header('Cache-Control: public, max-age=86400');
        readfile($path);
    }

    private function renderHome(array $data): void
    {
        $auth = $this->authViewData();
        // Ensure every page always receives the flash message; callers that
        // already call pullFlash() and pass 'flash' in $data take precedence.
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
