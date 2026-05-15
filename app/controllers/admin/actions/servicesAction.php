<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Actions;

use App\Controllers\Shared\AdminActionHandler;

class ServicesAction
{
    public function __invoke(): void
    {
        (new AdminActionHandler())->services();
    }
}
