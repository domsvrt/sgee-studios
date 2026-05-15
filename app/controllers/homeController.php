<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\BookingItemModel;
use App\Models\BookingModel;
use App\Models\NotificationModel;
use App\Models\ServiceCategoryModel;
use App\Models\UserModel;

class HomeController extends BaseController
{
    private UserModel $users;
    private ServiceCategoryModel $categories;
    private BookingModel $bookings;
    private BookingItemModel $bookingItems;
    private NotificationModel $notifications;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->categories = new ServiceCategoryModel();
        $this->bookings = new BookingModel();
        $this->bookingItems = new BookingItemModel();
        $this->notifications = new NotificationModel();
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
        $this->renderHome(['page' => 'book-now']);
    }

    public function signIn(): void
    {
        $this->renderHome(['page' => 'sign-in', 'flash' => $this->pullFlash()]);
    }

    public function signUp(): void
    {
        $this->renderHome(['page' => 'sign-up', 'flash' => $this->pullFlash()]);
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
            'role' => 'user',
            'status' => 'active',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created. Please sign in.'];
        $this->redirect('/sign-in');
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_first_name']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signed out.'];
        $this->redirect('/sign-in');
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

    private function renderHome(array $data): void
    {
        $auth = $this->authViewData();
        $this->render('home.php', array_merge($data, $auth));
    }

    private function authViewData(): array
    {
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        $role = $_SESSION['user_role'] ?? null;
        $isUser = $userId > 0 && $role === 'user';
        $firstName = trim((string) ($_SESSION['user_first_name'] ?? ''));

        if ($isUser && $firstName === '') {
            $user = $this->users->find($userId);
            if ($user) {
                $firstName = $this->users->firstName($user);
                $_SESSION['user_first_name'] = $firstName;
            }
        }

        return [
            'isLoggedIn' => $userId > 0,
            'isUser' => $isUser,
            'userFirstName' => $firstName ?: 'User',
            'notificationUnreadCount' => $isUser ? $this->notifications->unreadCountForUser($userId) : 0,
            'recentNotifications' => $isUser ? $this->notifications->recentForUser($userId, 5) : [],
        ];
    }

    private function requireUser(): int
    {
        $this->requireRole(['user']);
        return (int) ($_SESSION['user_id'] ?? 0);
    }
}
