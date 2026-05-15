<?php

declare(strict_types=1);

namespace App\Controllers\Admin\Actions;

use App\Controllers\AdminController;

class UpdateUserAction
{
    public function __invoke(): void
    {
        (new AdminController())->updateUser();
    }
}
