<?php

declare(strict_types=1);

namespace App\Tests\Logout;

use App\Entity\User;
use App\Tests\RouterTestTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractLogoutTest extends WebTestCase implements LogoutTestInterface
{
    use RouterTestTrait;

    protected string $logoutRouteName;
    protected string $loginRouteName;

    public function testSuccessfullyLogout(): void
    {
        $client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");
        /** @var User $user */
        $user = $entityManager->find(User::class, 1);

        $client->loginUser($user);
        $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->logoutRouteName));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame($this->loginRouteName);
    }
}
