<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $page = 'home';
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
        require __DIR__ . '/../views/home.php';
    }

    public function signUp(): void
    {
        $page = 'sign-up';
        require __DIR__ . '/../views/home.php';
    }
}
