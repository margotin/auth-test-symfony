<?php

declare(strict_types=1);

namespace App\Tests\ResetPassword;

interface ResetPasswordTestInterface
{
    public function testResetPasswordWithValidToken(): void;
    public function testResetPasswordWithInValidToken(): void;
    public function testResetPasswordWithExpiredToken(): void;
}
