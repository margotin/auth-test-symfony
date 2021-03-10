<?php

declare(strict_types=1);

namespace App\Tests\Logout;

class LogoutTest extends AbstractLogoutTest
{
    protected string $logoutRouteName = "security_logout";
    protected string $loginRouteName = "security_login";
}
