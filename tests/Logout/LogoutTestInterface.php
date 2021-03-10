<?php

declare(strict_types=1);

namespace App\Tests\Logout;

interface LogoutTestInterface
{
    public function testSuccessfullyLogout(): void;
}
