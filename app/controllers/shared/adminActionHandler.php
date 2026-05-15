<?php

declare(strict_types=1);

namespace App\Controllers\Shared;

use App\Models\BookingItemModel;
use App\Models\BookingModel;
use App\Models\BookingStatusLogModel;
use App\Models\ActivityLogModel;
use App\Models\NotificationModel;
use App\Models\PasswordResetRequestModel;
use App\Models\ServiceCategoryModel;
use App\Models\ServiceSectionModel;
use App\Models\ServiceModel;
use App\Models\UserModel;
use Throwable;

class AdminActionHandler extends BaseController
{
    use AdminGuardTrait;
    use AdminPayloadTrait;
    private UserModel $users;
    private ServiceCategoryModel $categories;
    private ServiceSectionModel $sections;
    private ServiceModel $services;
    private BookingModel $bookings;
    private BookingItemModel $bookingItems;
    private BookingStatusLogModel $logs;
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
        $this->logs = new BookingStatusLogModel();
        $this->notifications = new NotificationModel();
        $this->passwordResetRequests = new PasswordResetRequestModel();
        $this->activityLogs = new ActivityLogModel();
    }

    public function dashboard(): void
    {
        $this->renderAdmin('dashboard', [
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

    public function users(): void
    {
        $this->renderAdmin('users', [
            'title' => 'Users',
            'activeNav' => 'users',
            'users' => $this->users->all(),
        ]);
    }

    public function categories(): void
    {
        $this->renderAdmin('categories', [
            'title' => 'Categories',
            'activeNav' => 'categories',
            'categories' => $this->categories->all(),
        ]);
    }

    public function services(): void
    {
        $this->renderAdmin('services', [
            'title' => 'Services',
            'activeNav' => 'services',
            'services' => $this->services->all(),
            'categories' => $this->categories->all(),
            'sections' => $this->sections->all(),
        ]);
    }

    public function createServiceSection(): void
    {
        $this->handle(fn () => $this->sections->create($this->serviceSectionPayload()), '/admin/services', 'Service section created.');
    }

    public function updateServiceSection(): void
    {
        $this->handle(fn () => $this->sections->update($this->requiredId(), $this->serviceSectionPayload()), '/admin/services', 'Service section updated.');
    }

    public function deleteServiceSection(): void
    {
        $this->handle(fn () => $this->sections->delete($this->requiredId()), '/admin/services', 'Service section deleted.');
    }

    public function reorderServiceSections(): void
    {
        $this->handle(function (): void {
            $ids = $_POST['ordered_ids'] ?? [];
            if (!is_array($ids) || !$ids) {
                throw new \InvalidArgumentException('No service section order was provided.');
            }
            $this->sections->reorder($ids);
        }, '/admin/services', 'Service section order updated.');
    }

    public function bookings(): void
    {
        $this->renderAdmin('bookings', [
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
        $this->renderAdmin('logs', [
            'title' => 'Status Logs',
            'activeNav' => 'logs',
            'logs' => $this->logs->recent(100),
        ]);
    }

    public function passwordRequests(): void
    {
        $status = trim($_GET['status'] ?? '');
        $allowed = ['', 'pending', 'approved', 'rejected', 'completed'];
        if (!in_array($status, $allowed, true)) {
            $status = '';
        }

        $this->renderAdmin('passwordRequests', [
            'title' => 'Password Requests',
            'activeNav' => 'password-requests',
            'statusFilter' => $status,
            'requests' => $this->passwordResetRequests->all($status === '' ? null : $status, 300),
        ]);
    }

    public function activityLogs(): void
    {
        $this->renderAdmin('activityLogs', [
            'title' => 'Activity Logs',
            'activeNav' => 'activity-logs',
            'logs' => $this->activityLogs->recent(300),
        ]);
    }

    public function createUser(): void
    {
        $this->handle(function (): void {
            $password = trim($_POST['password'] ?? '');
            if ($password === '') {
                throw new \InvalidArgumentException('Password is required when creating a user.');
            }

            $payload = $this->userPayload($password);
            $this->users->create($payload);
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

    public function reorderCategories(): void
    {
        $this->handle(function (): void {
            $ids = $_POST['ordered_ids'] ?? [];
            if (!is_array($ids) || !$ids) {
                throw new \InvalidArgumentException('No category order was provided.');
            }
            $this->categories->reorder($ids);
        }, '/admin/categories', 'Category order updated.');
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

    public function reorderServices(): void
    {
        $this->handle(function (): void {
            $ids = $_POST['ordered_ids'] ?? [];
            if (!is_array($ids) || !$ids) {
                throw new \InvalidArgumentException('No service order was provided.');
            }
            $this->services->reorder($ids);
        }, '/admin/services', 'Service order updated.');
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
                $this->notifications->createBookingStatusNotification($old, $payload['status']);
                $this->activityLogs->create(
                    'booking_status',
                    'Booking status updated',
                    "Booking {$old['booking_code']} status changed to {$payload['status']}.",
                    (int) ($old['user_id'] ?? 0) ?: null,
                    (int) ($old['id'] ?? 0) ?: null
                );
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
                $this->notifications->createBookingStatusNotification($old, $status);
                $this->activityLogs->create(
                    'booking_status',
                    'Booking status updated',
                    "Booking {$old['booking_code']} status changed to {$status}.",
                    (int) ($old['user_id'] ?? 0) ?: null,
                    (int) ($old['id'] ?? 0) ?: null
                );
            }
        }, '/admin/bookings', 'Booking status updated.');
    }

    public function deleteBooking(): void
    {
        $this->handle(fn () => $this->bookings->delete($this->requiredId()), '/admin/bookings', 'Booking deleted.');
    }

    public function updatePasswordRequest(): void
    {
        $this->handle(function (): void {
            $id = $this->requiredId();
            $status = $this->enum($_POST['status'] ?? '', ['pending', 'approved', 'rejected', 'completed'], 'status');
            $notes = trim($_POST['notes'] ?? '') ?: null;
            $adminUserId = (int) ($_SESSION['user_id'] ?? 0);
            if ($adminUserId <= 0) {
                throw new \RuntimeException('Missing admin session.');
            }

            $previous = $this->passwordResetRequests->updateStatus($id, $status, $adminUserId, $notes);
            if (!$previous) {
                throw new \InvalidArgumentException('Password reset request not found.');
            }

            if ($status === 'completed') {
                $tempPassword = trim($_POST['temporary_password'] ?? '');
                if ($tempPassword === '') {
                    throw new \InvalidArgumentException('Temporary password is required when marking as completed.');
                }
                if (strlen($tempPassword) < 8) {
                    throw new \InvalidArgumentException('Temporary password must be at least 8 characters.');
                }

                $targetUserId = (int) ($previous['user_id'] ?? 0);
                if ($targetUserId <= 0) {
                    throw new \InvalidArgumentException('Cannot complete request without a linked user account.');
                }
                $this->users->updatePassword($targetUserId, password_hash($tempPassword, PASSWORD_DEFAULT));
            }

            $email = (string) ($previous['email_snapshot'] ?? '');
            $this->activityLogs->create(
                'password_reset_status',
                'Password reset request updated',
                "Password reset request for {$email} changed to {$status}.",
                (int) ($previous['user_id'] ?? 0) ?: null,
                null,
                [
                    'request_id' => $id,
                    'previous_status' => $previous['status'] ?? 'pending',
                    'new_status' => $status,
                ]
            );
        }, '/admin/password-requests', 'Password reset request updated.');
    }

}
