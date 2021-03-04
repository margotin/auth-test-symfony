<?php

declare(strict_types=1);

namespace App\Tests;

use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class LoginTest extends WebTestCase
{

    public const FIREWALL_CONTEXT = "main";

    /**
     * @param string $email
     * @dataProvider provideEmails
     */
    public function testSuccessfullyLogin(string $email): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = $client->getContainer()->get("router");

        $crawler = $client->request(Request::METHOD_GET, $router->generate("security_login"));

        $form = $crawler->filter("form[name=login]")->form([
            "email" => $email,
            "password" => "password"
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        /** @var SessionInterface $session */
        $session = $client->getContainer()->get("session");
        $sessionSecurityKey = "_security_" . self::FIREWALL_CONTEXT;

        $this->assertSame(true, $session->has($sessionSecurityKey));

        /** @var PostAuthenticationGuardToken $token */
        $token = unserialize($session->get($sessionSecurityKey));

        $this->assertSame(true, $token->isAuthenticated());
        $this->assertSame($email, $token->getUsername());
    }

    public function provideEmails(): Generator
    {
        yield ['toto@email.com'];
        yield ['tata@email.com'];
    }
}
