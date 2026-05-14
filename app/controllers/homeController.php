<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ServiceCategoryModel;
use App\Models\UserModel;

class HomeController
{
    private UserModel $users;
    private ServiceCategoryModel $categories;

    public function __construct()
    {
        $this->users = new UserModel();
        $this->categories = new ServiceCategoryModel();
    }

    public function index(): void
    {
        $page = 'home';
        $categories = array_values(array_filter(
            $this->categories->all(),
            static fn (array $category): bool => (int) ($category['is_active'] ?? 0) === 1
        ));
        require __DIR__ . '/../views/home.php';
    }

    public function about(): void
    {
        $page = 'about';
        require __DIR__ . '/../views/home.php';
    }

    public function contact(): void
    {
        $page = 'contact';
        require __DIR__ . '/../views/home.php';
    }

    public function bookNow(): void
    {
        $page = 'book-now';
        require __DIR__ . '/../views/home.php';
    }

    public function signIn(): void
    {
        $page = 'sign-in';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require __DIR__ . '/../views/home.php';
    }

    public function signUp(): void
    {
        $page = 'sign-up';
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require __DIR__ . '/../views/home.php';
    }

    public function doSignIn(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $user = $this->users->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash']) || ($user['status'] ?? '') !== 'active') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid credentials.'];
            header('Location: /sign-in');
            exit;
        }

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signed in successfully.'];
        $isAdminUser = in_array($_SESSION['user_role'], ['admin', 'manager'], true);
        header('Location: ' . ($isAdminUser ? '/admin' : '/'));
        exit;
    }

    public function doSignUp(): void
    {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($fullName === '' || $email === '' || $password === '') {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Name, email, and password are required.'];
            header('Location: /sign-up');
            exit;
        }

        if ($this->users->findByEmail($email)) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Email is already registered.'];
            header('Location: /sign-up');
            exit;
        }

        $id = $this->users->create([
            'full_name' => $fullName,
            'email' => $email,
            'phone' => null,
            'role' => 'user',
            'status' => 'active',
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $_SESSION['user_id'] = $id;
        $_SESSION['user_role'] = 'user';
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Account created and signed in.'];
        header('Location: /');
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user_role']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signed out.'];
        header('Location: /sign-in');
        exit;
    }
}
