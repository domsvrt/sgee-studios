<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Actions;

use App\Controllers\Shared\AdminActionHandler;

class UpdateServiceAction
{
    public function __invoke(): void
    {
        (new AdminActionHandler())->updateService();
    }
}
