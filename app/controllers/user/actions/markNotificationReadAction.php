<?php

declare(strict_types=1);

namespace App\Controllers\User\Actions;

use App\Controllers\HomeController;

class MarkNotificationReadAction
{
    public function __invoke(): void
    {
        (new HomeController())->markNotificationRead();
    }
}
