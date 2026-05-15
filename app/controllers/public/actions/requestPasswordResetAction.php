<?php

declare(strict_types=1);

namespace App\Controllers\Public\Actions;

use App\Controllers\Shared\HomeActionHandler;

class RequestPasswordResetAction
{
    public function __invoke(): void
    {
        (new HomeActionHandler())->requestPasswordReset();
    }
}
