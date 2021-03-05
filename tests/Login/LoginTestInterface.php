<?php

declare(strict_types=1);

namespace App\Tests\Login;

interface LoginTestInterface
{
    public function testSuccessfullyLogin(string $email, string $password): void;

    public function testInvalidCsrfToken(string $email, string $password): void;

    public function testInvalidEmail(string $email, string $password): void;

    public function testInvalidPassword(string $email, string $password): void;
}
