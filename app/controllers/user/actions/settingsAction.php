<?php

declare(strict_types=1);

namespace App\Controllers\User\Actions;

use App\Controllers\Shared\HomeActionHandler;

class SettingsAction
{
    public function __invoke(): void
    {
        (new HomeActionHandler())->settings();
    }
}
