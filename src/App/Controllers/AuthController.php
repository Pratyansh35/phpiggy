<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;
use App\Services\ValidatorService;
use Framework\TemplateEngine;

class AuthController
{
    public function __construct(
        private readonly UserService $s_user,
        private readonly TemplateEngine $view,
        private readonly ValidatorService $s_validator,
    )
    {}

    public function loginForm(): void
    {
        echo $this->view->render("/login.php", [
            'title' => 'Login'
        ]);
    }

    public function registerForm(): void
    {
        echo $this->view->render("/register.php", [
            'title' => 'Register'
        ]);
    }

    public function registerStore(): void
    {
        $this->s_validator->validateRegister($_POST);
        $this->s_user->isEmailTaken($_POST['email']);
        $this->s_user->create($_POST);

        redirectTo('/');
    }

    public function loginStore(): void
    {
        $this->s_validator->validateLogin($_POST);

        $this->s_user->login($_POST);

        redirectTo('/');
    }

    public function logout(): void
    {
        $this->s_user->logout();

        redirectTo('/');
    }
}
