<?php

declare(strict_types=1);

namespace App\Controllers;

class HomeController
{
    public function index(): void
    {
        $message = 'SGee Studios MVC project';
        require __DIR__ . '/../views/home.php';
    }
}
