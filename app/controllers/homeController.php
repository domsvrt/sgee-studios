<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ServiceCategoryModel;
use App\Models\UserModel;

class HomeController extends BaseController
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
        $categories = array_values(array_filter(
            $this->categories->all(),
            static fn (array $category): bool => (int) ($category['is_active'] ?? 0) === 1
        ));
        $this->render('home.php', ['page' => 'home', 'categories' => $categories]);
    }

    public function about(): void
    {
        $this->render('home.php', ['page' => 'about']);
    }

    public function contact(): void
    {
        $this->render('home.php', ['page' => 'contact']);
    }

    public function bookNow(): void
    {
        $this->render('home.php', ['page' => 'book-now']);
    }

    public function signIn(): void
    {
        $this->render('home.php', ['page' => 'sign-in', 'flash' => $this->pullFlash()]);
    }

    public function signUp(): void
    {
        $this->render('home.php', ['page' => 'sign-up', 'flash' => $this->pullFlash()]);
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
        unset($_SESSION['user_id'], $_SESSION['user_role']);
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Signed out.'];
        $this->redirect('/sign-in');
    }
}
