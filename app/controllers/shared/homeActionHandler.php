<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

use App\Models\Shared\BookingItemModel;
use App\Models\Shared\BookingModel;
use App\Models\Shared\ActivityLogModel;
use App\Models\Shared\NotificationModel;
use App\Models\Shared\PasswordResetRequestModel;
use App\Models\Shared\ServiceCategoryModel;
use App\Models\Shared\ServiceModel;
use App\Models\Shared\ServiceSectionModel;
use App\Models\Shared\UserModel;
use App\Services\DiceBearService;

class HomeActionHandler extends BaseController
{
    use HomeViewSupportTrait;
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
        $flash = $this->pullFlash();
        if (!$flash && (string) ($_GET['login_required'] ?? '') === '1') {
            $flash = ['type' => 'error', 'message' => 'you must login first'];
        }
        $this->renderHome(['page' => 'sign-in', 'flash' => $flash]);
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
        $_SESSION['user_full_name'] = trim((string) ($user['first_name'] ?? '') . ' ' . (string) ($user['last_name'] ?? ''));
        $_SESSION['user_email'] = (string) ($user['email'] ?? '');
        $_SESSION['user_phone'] = trim((string) ($user['phone'] ?? ''));
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

        if (!preg_match('/^09\d{9}$/', $phone)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Please enter a valid Philippine mobile number (11 digits, starts with 09).'];
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
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_first_name'], $_SESSION['user_full_name'], $_SESSION['user_email'], $_SESSION['user_phone'], $_SESSION['user_avatar']);
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

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Contact SGee Studios for your new password'];
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
        $_SESSION['user_full_name'] = trim($firstName . ' ' . $lastName);
        $_SESSION['user_email'] = $email;
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profile settings updated.'];
        $this->redirect('/settings');
    }

    public function submitBookingRequest(): void
    {
        $userId = $this->requireUser();
        $bookingDate = trim((string) ($_POST['booking_date'] ?? ''));
        $bookingTime = trim((string) ($_POST['booking_time'] ?? ''));
        $notes = trim((string) ($_POST['notes'] ?? ''));
        $categoryIdRaw = trim((string) ($_POST['category_id'] ?? ''));
        $serviceIds = $_POST['service_ids'] ?? [];

        if ($bookingDate === '' || $bookingTime === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Booking date and time are required.'];
            $this->redirect('/book-now');
        }

        if (!is_array($serviceIds) || !$serviceIds) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Select at least one service before submitting your booking.'];
            $this->redirect('/book-now');
        }

        $cleanServiceIds = array_values(array_unique(array_filter(array_map(static fn ($id): int => (int) $id, $serviceIds))));
        if (!$cleanServiceIds) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Select at least one valid service.'];
            $this->redirect('/book-now');
        }

        $bookingId = $this->bookings->create([
            'booking_code' => 'BK-' . strtoupper(bin2hex(random_bytes(4))),
            'user_id' => $userId,
            'category_id' => $categoryIdRaw === '' ? null : (int) $categoryIdRaw,
            'booking_date' => $bookingDate,
            'booking_time' => $bookingTime,
            'status' => 'pending',
            'notes' => $notes === '' ? null : $notes,
            'created_by_user_id' => $userId,
            'updated_by_user_id' => $userId,
        ]);
        $this->bookingItems->replaceForBooking($bookingId, $cleanServiceIds);
        $this->bookings->recalculateTotal($bookingId);
        $this->activityLogs->create(
            'booking_request',
            'Booking requested',
            "A new booking request {$bookingId} was submitted by user {$userId}.",
            $userId,
            $bookingId
        );

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Booking request submitted successfully.'];
        $this->redirect('/my-bookings');
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
        if ($file !== '') {
            $path = __DIR__ . '/../../../storage/users/' . $file;
            if (!is_file($path)) {
                http_response_code(404);
                echo 'Not found';
                return;
            }

            $mime = mime_content_type($path) ?: 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Cache-Control: public, max-age=86400');
            readfile($path);
            return;
        }

        $seed = trim((string) ($_GET['seed'] ?? ''));
        if ($seed === '') {
            http_response_code(404);
            echo 'Not found';
            return;
        }

        $style = trim((string) ($_GET['style'] ?? 'identicon'));
        if ($style === '') {
            $style = 'identicon';
        }
        $diceBear = new DiceBearService();
        $options = $style === 'identicon' ? $diceBear->defaultIdenticonOptions() : [];
        $diceBearUrl = $diceBear->avatarUrl($seed, $style, $options);
        $svg = @file_get_contents($diceBearUrl);
        if ($svg === false || trim($svg) === '') {
            $hash = substr(hash('sha256', $seed), 0, 30);
            $c1 = '#' . substr($hash, 0, 6);
            $c2 = '#' . substr($hash, 6, 6);
            $c3 = '#' . substr($hash, 12, 6);
            $c4 = '#' . substr($hash, 18, 6);
            $c5 = '#' . substr($hash, 24, 6);

            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96">'
                . '<rect width="96" height="96" fill="' . $c1 . '"/>'
                . '<rect x="8" y="8" width="36" height="36" rx="8" fill="' . $c2 . '" opacity="0.9"/>'
                . '<rect x="52" y="8" width="36" height="36" rx="8" fill="' . $c3 . '" opacity="0.9"/>'
                . '<rect x="8" y="52" width="36" height="36" rx="8" fill="' . $c4 . '" opacity="0.9"/>'
                . '<circle cx="70" cy="70" r="18" fill="' . $c5 . '" opacity="0.92"/>'
                . '</svg>';
        }

        header('Content-Type: image/svg+xml; charset=UTF-8');
        header('Cache-Control: public, max-age=60');
        echo $svg;
    }

}
