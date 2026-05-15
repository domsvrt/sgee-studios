<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Controllers\HomeController;

class SiteController
{
    private HomeController $home;

    public function __construct()
    {
        $this->home = new HomeController();
    }

    public function index(): void { $this->home->index(); }
    public function about(): void { $this->home->about(); }
    public function contact(): void { $this->home->contact(); }
    public function bookNow(): void { $this->home->bookNow(); }
    public function signIn(): void { $this->home->signIn(); }
    public function signUp(): void { $this->home->signUp(); }
    public function forgotPassword(): void { $this->home->forgotPassword(); }
    public function doSignIn(): void { $this->home->doSignIn(); }
    public function doSignUp(): void { $this->home->doSignUp(); }
    public function requestPasswordReset(): void { $this->home->requestPasswordReset(); }
    public function logout(): void { $this->home->logout(); }
    public function userAvatar(): void { $this->home->userAvatar(); }
}
