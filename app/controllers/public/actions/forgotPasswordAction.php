<?php

declare(strict_types=1);

namespace App\Controllers\Public\Actions;

use App\Controllers\HomeController;

class ForgotPasswordAction
{
    public function __invoke(): void
    {
        (new HomeController())->forgotPassword();
    }
}
