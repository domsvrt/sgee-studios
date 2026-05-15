<?php

declare(strict_types=1);

session_start();

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

use App\Controllers\Admin\Actions\ActivityLogsAction;
use App\Controllers\Admin\Actions\BookingsAction;
use App\Controllers\Admin\Actions\CategoriesAction;
use App\Controllers\Admin\Actions\CreateBookingAction;
use App\Controllers\Admin\Actions\CreateCategoryAction;
use App\Controllers\Admin\Actions\CreateServiceAction;
use App\Controllers\Admin\Actions\CreateServiceSectionAction;
use App\Controllers\Admin\Actions\CreateUserAction;
use App\Controllers\Admin\Actions\DashboardAction;
use App\Controllers\Admin\Actions\DeleteBookingAction;
use App\Controllers\Admin\Actions\DeleteCategoryAction;
use App\Controllers\Admin\Actions\DeleteServiceAction;
use App\Controllers\Admin\Actions\DeleteServiceSectionAction;
use App\Controllers\Admin\Actions\DeleteUserAction;
use App\Controllers\Admin\Actions\LogsAction;
use App\Controllers\Admin\Actions\PasswordRequestsAction;
use App\Controllers\Admin\Actions\ReorderCategoriesAction;
use App\Controllers\Admin\Actions\ReorderServicesAction;
use App\Controllers\Admin\Actions\ReorderServiceSectionsAction;
use App\Controllers\Admin\Actions\ServicesAction;
use App\Controllers\Admin\Actions\UpdateBookingAction;
use App\Controllers\Admin\Actions\UpdateBookingStatusAction;
use App\Controllers\Admin\Actions\UpdateCategoryAction;
use App\Controllers\Admin\Actions\UpdatePasswordRequestAction;
use App\Controllers\Admin\Actions\UpdateServiceAction;
use App\Controllers\Admin\Actions\UpdateServiceSectionAction;
use App\Controllers\Admin\Actions\UpdateUserAction;
use App\Controllers\Admin\Actions\UsersAction;
use App\Controllers\Public\Actions\AboutAction;
use App\Controllers\Public\Actions\BookNowAction;
use App\Controllers\Public\Actions\ContactAction;
use App\Controllers\Public\Actions\DoSignInAction;
use App\Controllers\Public\Actions\DoSignUpAction;
use App\Controllers\Public\Actions\ForgotPasswordAction;
use App\Controllers\Public\Actions\IndexAction;
use App\Controllers\Public\Actions\LogoutAction;
use App\Controllers\Public\Actions\RequestPasswordResetAction;
use App\Controllers\Public\Actions\SignInAction;
use App\Controllers\Public\Actions\SignUpAction;
use App\Controllers\Public\Actions\UserAvatarAction;
use App\Controllers\User\Actions\MarkAllNotificationsReadAction;
use App\Controllers\User\Actions\MarkNotificationReadAction;
use App\Controllers\User\Actions\MyBookingsAction;
use App\Controllers\User\Actions\NotificationsAction;
use App\Controllers\User\Actions\SettingsAction;
use App\Controllers\User\Actions\UpdatePasswordSettingsAction;
use App\Controllers\User\Actions\UpdateProfileSettingsAction;
use App\Controllers\User\Actions\UploadAvatarAction;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$routeMethod = $method === 'HEAD' ? 'GET' : $method;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$publicRoutes = [
    'GET' => [
        '/' => IndexAction::class,
        '/about' => AboutAction::class,
        '/contact' => ContactAction::class,
        '/book-now' => BookNowAction::class,
        '/sign-in' => SignInAction::class,
        '/sign-up' => SignUpAction::class,
        '/forgot-password' => ForgotPasswordAction::class,
        '/user-avatar' => UserAvatarAction::class,
    ],
    'POST' => [
        '/sign-in' => DoSignInAction::class,
        '/sign-up' => DoSignUpAction::class,
        '/forgot-password' => RequestPasswordResetAction::class,
        '/logout' => LogoutAction::class,
    ],
];

$userRoutes = [
    'GET' => [
        '/my-bookings' => MyBookingsAction::class,
        '/notifications' => NotificationsAction::class,
        '/settings' => SettingsAction::class,
    ],
    'POST' => [
        '/profile/avatar' => UploadAvatarAction::class,
        '/settings/profile' => UpdateProfileSettingsAction::class,
        '/settings/password' => UpdatePasswordSettingsAction::class,
        '/notifications/read' => MarkNotificationReadAction::class,
        '/notifications/read-all' => MarkAllNotificationsReadAction::class,
    ],
];

if (isset($publicRoutes[$routeMethod][$path])) {
    (new $publicRoutes[$routeMethod][$path])();
    exit;
}

if (isset($userRoutes[$routeMethod][$path])) {
    (new $userRoutes[$routeMethod][$path])();
    exit;
}

if (str_starts_with($path, '/admin')) {
    $adminRoutes = [
        'GET' => [
            '/admin' => DashboardAction::class,
            '/admin/users' => UsersAction::class,
            '/admin/categories' => CategoriesAction::class,
            '/admin/services' => ServicesAction::class,
            '/admin/bookings' => BookingsAction::class,
            '/admin/logs' => LogsAction::class,
            '/admin/password-requests' => PasswordRequestsAction::class,
            '/admin/activity-logs' => ActivityLogsAction::class,
        ],
        'POST' => [
            '/admin/users/create' => CreateUserAction::class,
            '/admin/users/update' => UpdateUserAction::class,
            '/admin/users/delete' => DeleteUserAction::class,
            '/admin/categories/create' => CreateCategoryAction::class,
            '/admin/categories/update' => UpdateCategoryAction::class,
            '/admin/categories/delete' => DeleteCategoryAction::class,
            '/admin/categories/reorder' => ReorderCategoriesAction::class,
            '/admin/services/create' => CreateServiceAction::class,
            '/admin/services/update' => UpdateServiceAction::class,
            '/admin/services/delete' => DeleteServiceAction::class,
            '/admin/services/reorder' => ReorderServicesAction::class,
            '/admin/service-sections/create' => CreateServiceSectionAction::class,
            '/admin/service-sections/update' => UpdateServiceSectionAction::class,
            '/admin/service-sections/delete' => DeleteServiceSectionAction::class,
            '/admin/service-sections/reorder' => ReorderServiceSectionsAction::class,
            '/admin/bookings/create' => CreateBookingAction::class,
            '/admin/bookings/update' => UpdateBookingAction::class,
            '/admin/bookings/status' => UpdateBookingStatusAction::class,
            '/admin/bookings/delete' => DeleteBookingAction::class,
            '/admin/password-requests/update' => UpdatePasswordRequestAction::class,
        ],
    ];

    if (isset($adminRoutes[$routeMethod][$path])) {
        (new $adminRoutes[$routeMethod][$path])();
        exit;
    }
}

http_response_code(404);
echo '404 Not Found';
