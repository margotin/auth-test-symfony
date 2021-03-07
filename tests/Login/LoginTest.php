<?php

declare(strict_types=1);

namespace App\Tests\Login;

use App\Repository\UserRepository;
use Generator;

class LoginTest extends AbstractLoginTest
{
    protected string $firewallContext = "main";
    protected string $formSelector = "form[name=login]";
    protected string $loginRouteName = "security_login";
    protected string $idUsedWhenGeneratingTheToken = "authenticate";
    protected string $userRepository = UserRepository::class;

    public function provideValidEmailsAndValidPasswords(): Generator
    {
        yield ['toto@email.com', "password"];
        yield ['tata@email.com', "password"];
    }

    public function provideValidEmailsAndInvalidPasswords(): Generator
    {
        yield ['toto@email.com', "fail"];
        yield ['tata@email.com', "fail"];
    }

    public function provideInvalidEmailsAndValidPasswords(): Generator
    {
        yield ['fail@email.com', "password"];
        yield ['fail@email.com', "password"];
    }
}
