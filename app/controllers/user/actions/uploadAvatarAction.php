<?php

declare(strict_types=1);

namespace App\Controllers\User\Actions;

use App\Controllers\HomeController;

class UploadAvatarAction
{
    public function __invoke(): void
    {
        (new HomeController())->uploadAvatar();
    }
}
