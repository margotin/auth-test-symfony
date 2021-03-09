<?php

declare(strict_types=1);

namespace App\Tests\ForgottenPassword;

use App\Dto\ForgottenPasswordDTO;
use App\Repository\UserRepository;
use Generator;

class ForgottenPasswordTest extends AbstractForgottenPasswordTest
{
    protected string $formSelector = "form[name=forgotten_password]";
    protected string $formEmailField = "forgotten_password[email]";
    protected string $loginRouteName = "security_forgotten_password";
    protected string $userRepository = UserRepository::class;
    protected string $redirectRouteName = "security_login";
    protected string $nameCsrfToken = "forgotten_password[_token]";
    protected string $idUsedWhenGeneratingTheToken = ForgottenPasswordDTO::class;

    public function provideValidEmails(): Generator
    {
        yield ["toto@email.com"];
        yield ["tata@email.com"];
    }

    public function provideInvalidEmails(): Generator
    {
        yield ["fail@email.com"];
        yield ["fail@email"];
    }
}
