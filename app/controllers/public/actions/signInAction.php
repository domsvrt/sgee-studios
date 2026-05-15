<?php

declare(strict_types=1);

namespace App\Controllers\Public\Actions;

use App\Controllers\HomeController;

class SignInAction
{
    public function __invoke(): void
    {
        (new HomeController())->signIn();
    }
}
