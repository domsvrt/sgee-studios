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

use App\Controllers\HomeController;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$routeMethod = $method === 'HEAD' ? 'GET' : $method;
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

$routes = [
    'GET' => [
        '/' => [new HomeController(), 'index'],
        '/about' => [new HomeController(), 'about'],
        '/contact' => [new HomeController(), 'contact'],
        '/book-now' => [new HomeController(), 'bookNow'],
        '/sign-in' => [new HomeController(), 'signIn'],
        '/sign-up' => [new HomeController(), 'signUp'],
    ],
    'POST' => [
        '/sign-in' => [new HomeController(), 'doSignIn'],
        '/sign-up' => [new HomeController(), 'doSignUp'],
        '/logout' => [new HomeController(), 'logout'],
    ],
];

if (isset($routes[$routeMethod][$path])) {
    call_user_func($routes[$routeMethod][$path]);
    exit;
}

if (str_starts_with($path, '/admin')) {
    $admin = new \App\Controllers\AdminController();
    $adminRoutes = [
        'GET' => [
            '/admin' => [$admin, 'dashboard'],
            '/admin/users' => [$admin, 'users'],
            '/admin/categories' => [$admin, 'categories'],
            '/admin/services' => [$admin, 'services'],
            '/admin/bookings' => [$admin, 'bookings'],
            '/admin/logs' => [$admin, 'logs'],
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
            '/admin/bookings/create' => [$admin, 'createBooking'],
            '/admin/bookings/update' => [$admin, 'updateBooking'],
            '/admin/bookings/status' => [$admin, 'updateBookingStatus'],
            '/admin/bookings/delete' => [$admin, 'deleteBooking'],
        ],
    ];

    if (isset($adminRoutes[$routeMethod][$path])) {
        call_user_func($adminRoutes[$routeMethod][$path]);
        exit;
    }
}

http_response_code(404);
echo '404 Not Found';
