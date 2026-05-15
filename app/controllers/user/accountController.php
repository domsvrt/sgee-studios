<?php

declare(strict_types=1);

namespace App\Controllers\User;

use App\Controllers\Shared\HomeActionHandler;

class AccountController
{
    private HomeActionHandler $home;

    public function __construct()
    {
        $this->home = new HomeActionHandler();
    }

    public function myBookings(): void { $this->home->myBookings(); }
    public function notifications(): void { $this->home->notifications(); }
    public function settings(): void { $this->home->settings(); }
    public function uploadAvatar(): void { $this->home->uploadAvatar(); }
    public function updateProfileSettings(): void { $this->home->updateProfileSettings(); }
    public function updatePasswordSettings(): void { $this->home->updatePasswordSettings(); }
    public function markNotificationRead(): void { $this->home->markNotificationRead(); }
    public function markAllNotificationsRead(): void { $this->home->markAllNotificationsRead(); }
}
