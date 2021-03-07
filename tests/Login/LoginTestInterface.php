<?php

declare(strict_types=1);

namespace App\Tests\Login;

use Generator;

interface LoginTestInterface
{
    public function testSuccessfullyLogin(string $email, string $password): void;

    public function testInvalidCsrfToken(string $email, string $password): void;

    public function testInvalidEmail(string $email, string $password): void;

    public function testInvalidPassword(string $email, string $password): void;

    public function provideValidEmailsAndValidPasswords(): Generator;

    public function provideValidEmailsAndInvalidPasswords(): Generator;

    public function provideInvalidEmailsAndValidPasswords(): Generator;
}
