<?php

declare(strict_types=1);

namespace App\Controllers\Public\Actions;

use App\Controllers\Shared\HomeActionHandler;

class ForgotPasswordAction
{
    public function __invoke(): void
    {
        (new HomeActionHandler())->forgotPassword();
    }
}
