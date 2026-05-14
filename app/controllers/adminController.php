<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BookingItemModel;
use App\Models\BookingModel;
use App\Models\BookingStatusLogModel;
use App\Models\ServiceCategoryModel;
use App\Models\ServiceModel;
use App\Models\UserModel;
use Throwable;

class AdminController
{
    private UserModel $users;
    private ServiceCategoryModel $categories;
    private ServiceModel $services;
    private BookingModel $bookings;
    private BookingItemModel $bookingItems;
    private BookingStatusLogModel $logs;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->categories = new ServiceCategoryModel();
        $this->services = new ServiceModel();
        $this->bookings = new BookingModel();
        $this->bookingItems = new BookingItemModel();
        $this->logs = new BookingStatusLogModel();
    }

    public function dashboard(): void
    {
        $this->render('dashboard', [
            'title' => 'Dashboard',
            'activeNav' => 'dashboard',
            'metrics' => [
                'users' => $this->users->count(),
                'admins' => $this->users->adminCount(),
                'activeServices' => $this->services->activeCount(),
                'upcomingBookings' => $this->bookings->upcomingCount(),
                'pendingBookings' => $this->bookings->countByStatus('pending'),
                'completedBookings' => $this->bookings->countByStatus('completed'),
            ],
            'upcoming' => $this->bookings->upcoming(),
            'statusCounts' => $this->bookings->statusCounts(),
            'recentLogs' => $this->logs->recent(6),
        ]);
    }

    public function loginForm(): void
    {
        if ($this->users->adminCount() === 0 || isset($_SESSION['admin_user_id'])) {
            header('Location: /admin');
            exit;
        }

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        $e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        require __DIR__ . '/../views/admin/login.php';
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $admin = $this->users->findActiveAdminByEmail($email);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid admin credentials.'];
            header('Location: /admin/login');
            exit;
        }

        $_SESSION['admin_user_id'] = (int) $admin['id'];
        header('Location: /admin');
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION['admin_user_id']);
        header('Location: /admin/login');
        exit;
    }

    public function users(): void
    {
        $this->render('users', [
            'title' => 'Users',
            'activeNav' => 'users',
            'users' => $this->users->all(),
        ]);
    }

    public function categories(): void
    {
        $this->render('categories', [
            'title' => 'Categories',
            'activeNav' => 'categories',
            'categories' => $this->categories->all(),
        ]);
    }

    public function services(): void
    {
        $this->render('services', [
            'title' => 'Services',
            'activeNav' => 'services',
            'services' => $this->services->all(),
            'categories' => $this->categories->all(),
        ]);
    }

    public function bookings(): void
    {
        $this->render('bookings', [
            'title' => 'Bookings',
            'activeNav' => 'bookings',
            'bookings' => $this->bookings->all(),
            'bookingItems' => $this->bookingItems->groupedByBooking(),
            'users' => $this->users->bookers(),
            'categories' => $this->categories->all(),
            'services' => $this->services->all(),
        ]);
    }

    public function logs(): void
    {
        $this->render('logs', [
            'title' => 'Status Logs',
            'activeNav' => 'logs',
            'logs' => $this->logs->recent(100),
        ]);
    }

    public function createUser(): void
    {
        $this->handle(function (): void {
            $hadNoAdmins = $this->users->adminCount() === 0;
            $password = trim($_POST['password'] ?? '');
            if ($password === '') {
                throw new \InvalidArgumentException('Password is required when creating a user.');
            }

            $payload = $this->userPayload($password);
            $id = $this->users->create($payload);

            if ($hadNoAdmins && $payload['role'] === 'admin') {
                $_SESSION['admin_user_id'] = $id;
            }
        }, '/admin/users', 'User created.');
    }

    public function updateUser(): void
    {
        $this->handle(function (): void {
            $id = $this->requiredId();
            $password = trim($_POST['password'] ?? '');
            $this->users->update($id, $this->userPayload($password !== '' ? $password : null));
        }, '/admin/users', 'User updated.');
    }

    public function deleteUser(): void
    {
        $this->handle(fn () => $this->users->delete($this->requiredId()), '/admin/users', 'User deleted.');
    }

    public function createCategory(): void
    {
        $this->handle(fn () => $this->categories->create($this->categoryPayload()), '/admin/categories', 'Category created.');
    }

    public function updateCategory(): void
    {
        $this->handle(fn () => $this->categories->update($this->requiredId(), $this->categoryPayload()), '/admin/categories', 'Category updated.');
    }

    public function deleteCategory(): void
    {
        $this->handle(fn () => $this->categories->delete($this->requiredId()), '/admin/categories', 'Category deleted.');
    }

    public function createService(): void
    {
        $this->handle(fn () => $this->services->create($this->servicePayload()), '/admin/services', 'Service created.');
    }

    public function updateService(): void
    {
        $this->handle(fn () => $this->services->update($this->requiredId(), $this->servicePayload()), '/admin/services', 'Service updated.');
    }

    public function deleteService(): void
    {
        $this->handle(fn () => $this->services->delete($this->requiredId()), '/admin/services', 'Service deleted.');
    }

    public function createBooking(): void
    {
        $this->handle(function (): void {
            $bookingId = $this->bookings->create($this->bookingPayload());
            $this->bookingItems->replaceForBooking($bookingId, $_POST['service_ids'] ?? []);
            $this->bookings->recalculateTotal($bookingId);
        }, '/admin/bookings', 'Booking created.');
    }

    public function updateBooking(): void
    {
        $this->handle(function (): void {
            $id = $this->requiredId();
            $old = $this->bookings->find($id);
            $payload = $this->bookingPayload();
            $this->bookings->update($id, $payload);
            $this->bookingItems->replaceForBooking($id, $_POST['service_ids'] ?? []);
            $this->bookings->recalculateTotal($id);

            if ($old && $old['status'] !== $payload['status']) {
                $this->logs->create($id, $old['status'], $payload['status'], null, 'Status changed from booking edit.');
            }
        }, '/admin/bookings', 'Booking updated.');
    }

    public function updateBookingStatus(): void
    {
        $this->handle(function (): void {
            $id = $this->requiredId();
            $status = $this->enum($_POST['status'] ?? '', ['pending', 'confirmed', 'completed', 'cancelled'], 'status');
            $old = $this->bookings->updateStatus($id, $status, null);

            if ($old && $old['status'] !== $status) {
                $this->logs->create($id, $old['status'], $status, null, trim($_POST['change_note'] ?? '') ?: null);
            }
        }, '/admin/bookings', 'Booking status updated.');
    }

    public function deleteBooking(): void
    {
        $this->handle(fn () => $this->bookings->delete($this->requiredId()), '/admin/bookings', 'Booking deleted.');
    }

    private function render(string $view, array $data): void
    {
        $this->ensureAdminAccess();
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $e = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        extract($data, EXTR_SKIP);

        ob_start();
        require __DIR__ . "/../views/admin/{$view}.php";
        $content = ob_get_clean();

        require __DIR__ . '/../views/admin/layout.php';
    }

    private function handle(callable $action, string $redirect, string $success): void
    {
        $this->ensureAdminAccess();

        try {
            $action();
            $_SESSION['flash'] = ['type' => 'success', 'message' => $success];
        } catch (Throwable $exception) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => $exception->getMessage()];
        }

        header("Location: {$redirect}");
        exit;
    }

    private function ensureAdminAccess(): void
    {
        if (isset($_SESSION['admin_user_id'])) {
            return;
        }

        if ($this->users->adminCount() === 0) {
            return;
        }

        header('Location: /admin/login');
        exit;
    }

    private function userPayload(?string $password): array
    {
        $role = $this->enum($_POST['role'] ?? 'user', ['admin', 'user'], 'role');
        $payload = [
            'full_name' => $this->requiredString('full_name'),
            'email' => $this->requiredString('email'),
            'phone' => trim($_POST['phone'] ?? '') ?: null,
            'role' => $role,
            'admin_level' => $role === 'admin' ? $this->enum($_POST['admin_level'] ?? 'manager', ['owner', 'manager'], 'admin level') : null,
            'status' => $this->enum($_POST['status'] ?? 'active', ['active', 'inactive', 'banned'], 'status'),
        ];

        if ($password !== null) {
            $payload['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            $payload['visible_password'] = $password;
        }

        return $payload;
    }

    private function categoryPayload(): array
    {
        return [
            'slug' => $this->slug($this->requiredString('slug')),
            'name' => $this->requiredString('name'),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];
    }

    private function servicePayload(): array
    {
        $price = (float) ($_POST['price'] ?? 0);
        if ($price < 0) {
            throw new \InvalidArgumentException('Price cannot be negative.');
        }

        return [
            'category_id' => (int) $this->requiredString('category_id'),
            'code' => $this->slug($this->requiredString('code')),
            'name' => $this->requiredString('name'),
            'description' => trim($_POST['description'] ?? '') ?: null,
            'price' => number_format($price, 2, '.', ''),
            'unit_label' => trim($_POST['unit_label'] ?? '') ?: null,
            'selection_type' => $this->enum($_POST['selection_type'] ?? 'multiple', ['single', 'multiple', 'quantity'], 'selection type'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
        ];
    }

    private function bookingPayload(): array
    {
        $code = trim($_POST['booking_code'] ?? '') ?: 'BK-' . strtoupper(bin2hex(random_bytes(4)));
        $categoryId = trim($_POST['category_id'] ?? '');

        return [
            'booking_code' => $code,
            'user_id' => (int) $this->requiredString('user_id'),
            'category_id' => $categoryId === '' ? null : (int) $categoryId,
            'booking_date' => $this->requiredString('booking_date'),
            'booking_time' => $this->requiredString('booking_time'),
            'status' => $this->enum($_POST['status'] ?? 'pending', ['pending', 'confirmed', 'completed', 'cancelled'], 'status'),
            'notes' => trim($_POST['notes'] ?? '') ?: null,
            'created_by_user_id' => $_SESSION['admin_user_id'] ?? null,
            'updated_by_user_id' => $_SESSION['admin_user_id'] ?? null,
        ];
    }

    private function requiredId(): int
    {
        return (int) $this->requiredString('id');
    }

    private function requiredString(string $key): string
    {
        $value = trim($_POST[$key] ?? '');

        if ($value === '') {
            throw new \InvalidArgumentException(str_replace('_', ' ', ucfirst($key)) . ' is required.');
        }

        return $value;
    }

    private function enum(string $value, array $allowed, string $label): string
    {
        if (!in_array($value, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid {$label}.");
        }

        return $value;
    }

    private function slug(string $value): string
    {
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9_-]+/', '-', $slug) ?: '';
        $slug = trim($slug, '-_');

        if ($slug === '') {
            throw new \InvalidArgumentException('Slug/code is required.');
        }

        return $slug;
    }
}
