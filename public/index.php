<?php

declare(strict_types=1);

session_start();

$appTimezone = getenv('APP_TIMEZONE') ?: 'Asia/Manila';
date_default_timezone_set($appTimezone);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $parts = explode('\\', $relativeClass);
    $className = array_pop($parts);
    $pathParts = array_map(static fn (string $part): string => strtolower($part), $parts);
    $fileName = lcfirst($className) . '.php';
    $file = $baseDir . implode('/', $pathParts) . '/' . $fileName;

    if (is_file($file)) {
        require $file;
    }
});

use App\Controllers\Admin\AdminController;
use App\Controllers\Public\PublicController;
use App\Controllers\User\UserController;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$routeMethod = $method === 'HEAD' ? 'GET' : $method;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$public = new PublicController();
$user = new UserController();
$admin = new AdminController();

$publicRoutes = [
    'GET' => [
        '/' => [$public, 'index'],
        '/about' => [$public, 'about'],
        '/contact' => [$public, 'contact'],
        '/book-now' => [$public, 'bookNow'],
        '/sign-in' => [$public, 'signIn'],
        '/sign-up' => [$public, 'signUp'],
        '/forgot-password' => [$public, 'forgotPassword'],
        '/user-avatar' => [$public, 'userAvatar'],
    ],
    'POST' => [
        '/sign-in' => [$public, 'doSignIn'],
        '/sign-up' => [$public, 'doSignUp'],
        '/forgot-password' => [$public, 'requestPasswordReset'],
        '/logout' => [$public, 'logout'],
    ],
];

$userRoutes = [
    'GET' => [
        '/my-bookings' => [$user, 'myBookings'],
        '/notifications' => [$user, 'notifications'],
        '/settings' => [$user, 'settings'],
    ],
    'POST' => [
        '/book-now' => [$user, 'submitBookingRequest'],
        '/profile/avatar' => [$user, 'uploadAvatar'],
        '/settings/profile' => [$user, 'updateProfileSettings'],
        '/settings/password' => [$user, 'updatePasswordSettings'],
        '/notifications/read' => [$user, 'markNotificationRead'],
        '/notifications/read-all' => [$user, 'markAllNotificationsRead'],
    ],
];

if (isset($publicRoutes[$routeMethod][$path])) {
    call_user_func($publicRoutes[$routeMethod][$path]);
    exit;
}

if (isset($userRoutes[$routeMethod][$path])) {
    call_user_func($userRoutes[$routeMethod][$path]);
    exit;
}

if (str_starts_with($path, '/admin')) {
    $adminRoutes = [
        'GET' => [
            '/admin' => [$admin, 'dashboard'],
            '/admin/analytics' => [$admin, 'analytics'],
            '/admin/users' => [$admin, 'users'],
            '/admin/categories' => [$admin, 'categories'],
            '/admin/services' => [$admin, 'services'],
            '/admin/bookings' => [$admin, 'bookings'],
            '/admin/logs' => [$admin, 'logs'],
            '/admin/password-requests' => [$admin, 'passwordRequests'],
            '/admin/activity-logs' => [$admin, 'activityLogs'],
        ],
        'POST' => [
            '/admin/users/create' => [$admin, 'createUser'],
            '/admin/users/update' => [$admin, 'updateUser'],
            '/admin/users/delete' => [$admin, 'deleteUser'],
            '/admin/categories/create' => [$admin, 'createCategory'],
            '/admin/categories/update' => [$admin, 'updateCategory'],
            '/admin/categories/delete' => [$admin, 'deleteCategory'],
            '/admin/categories/reorder' => [$admin, 'reorderCategories'],
            '/admin/services/create' => [$admin, 'createService'],
            '/admin/services/update' => [$admin, 'updateService'],
            '/admin/services/delete' => [$admin, 'deleteService'],
            '/admin/services/reorder' => [$admin, 'reorderServices'],
            '/admin/service-sections/create' => [$admin, 'createServiceSection'],
            '/admin/service-sections/update' => [$admin, 'updateServiceSection'],
            '/admin/service-sections/delete' => [$admin, 'deleteServiceSection'],
            '/admin/service-sections/reorder' => [$admin, 'reorderServiceSections'],
            '/admin/bookings/create' => [$admin, 'createBooking'],
            '/admin/bookings/update' => [$admin, 'updateBooking'],
            '/admin/bookings/status' => [$admin, 'updateBookingStatus'],
            '/admin/bookings/delete' => [$admin, 'deleteBooking'],
            '/admin/password-requests/update' => [$admin, 'updatePasswordRequest'],
        ],
    ];

    if (isset($adminRoutes[$routeMethod][$path])) {
        call_user_func($adminRoutes[$routeMethod][$path]);
        exit;
    }
}

http_response_code(404);
echo '404 Not Found';
