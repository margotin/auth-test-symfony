<?php

declare(strict_types=1);

namespace App\Tests\ForgottenPassword;

use Generator;

interface ForgottenPasswordTestInterface
{
    public function testSuccessfullyForgottenPassword(string $email): void;

    public function provideValidEmails(): Generator;

    public function provideInvalidEmails(): Generator;
}
