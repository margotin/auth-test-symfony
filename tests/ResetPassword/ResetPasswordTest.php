<?php

declare(strict_types=1);

namespace App\Tests\ResetPassword;

use App\Repository\UserRepository;

class ResetPasswordTest extends AbstractResetPasswordTest
{
    protected string $resetPasswordRouteName = "security_reset_password";
    protected string $forgottenPasswordRouteName = "security_forgotten_password";
    protected string $loginRouteName = "security_login";
    protected string $userRepository = UserRepository::class;
    protected string $validEmail = "toto@email.com";
    protected string $formSelector = "form[name=reset_password]";
    protected string $formPasswordField = "reset_password[plainPassword]";
}