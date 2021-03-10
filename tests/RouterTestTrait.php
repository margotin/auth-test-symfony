<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Component\Routing\RouterInterface;

trait RouterTestTrait
{
    public function getRouter(): RouterInterface
    {
        return static::$container->get("router");
    }

}