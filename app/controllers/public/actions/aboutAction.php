<?php

declare(strict_types=1);

namespace App\Controllers\Public\Actions;

use App\Controllers\Shared\HomeActionHandler;

class AboutAction
{
    public function __invoke(): void
    {
        (new HomeActionHandler())->about();
    }
}
