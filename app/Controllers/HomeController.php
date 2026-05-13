<?php

namespace App\Controllers;

class HomeController
{
    public function index()
    {
        $message = "this is mvc project";
        require_once __DIR__ . '/../Views/home.php';
    }
}
