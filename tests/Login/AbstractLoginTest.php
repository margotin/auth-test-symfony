<?php

declare(strict_types=1);

namespace App\Tests\Login;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

abstract class AbstractLoginTest extends WebTestCase implements LoginTestInterface
{
    protected string $firewallContext;
    protected string $formSelector;
    protected string $loginRouteName;
    protected string $idUsedWhenGeneratingTheToken;
    protected string $userRepository;

    /**
     * @param string $email
     * @param string $password
     * @dataProvider provideValidEmailsAndValidPasswords
     */
    public function testSuccessfullyLogin(string $email, string $password): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = static::$container->get("router");
        $crawler = $client->request(Request::METHOD_GET, $router->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            "email" => $email,
            "password" => $password
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var SessionInterface $session */
        $session = static::$container->get("session");
        $sessionSecurityKey = "_security_" . $this->firewallContext;
        $this->assertTrue($session->has($sessionSecurityKey));

        /** @var PostAuthenticationGuardToken $token */
        $token = unserialize($session->get($sessionSecurityKey));
        $this->assertTrue($token->isAuthenticated());
        $this->assertSame($email, $token->getUsername());
    }

    /**
     * @param string $email
     * @param string $password
     * @dataProvider provideValidEmailsAndValidPasswords
     */
    public function testInvalidCsrfToken(string $email, string $password): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = static::$container->get("router");
        $crawler = $client->request(Request::METHOD_GET, $router->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            "_csrf_token" => "fail",
            "email" => $email,
            "password" => $password
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $token = $client->getRequest()->get("_csrf_token");
        $this->assertFalse(
            static::$container
                ->get('security.csrf.token_manager')
                ->isTokenValid(new CsrfToken($this->idUsedWhenGeneratingTheToken, $token))
        );
    }

    /**
     * @param string $email
     * @param string $password
     * @dataProvider provideValidEmailsAndInvalidPasswords
     */
    public function testInvalidPassword(string $email, string $password): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = static::$container->get("router");
        $crawler = $client->request(Request::METHOD_GET, $router->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            "email" => $email,
            "password" => $password
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $userRepository = static::$container->get($this->userRepository);
        /** @var UserInterface $user */
        $user = $userRepository->findOneBy(["email" => $email]);
        $this->assertSame($user->getUsername(), $client->getRequest()->request->get("email"));

        /** @var  UserPasswordEncoderInterface $encoder */
        $encoder = static::$container->get(UserPasswordEncoderInterface::class);
        $this->assertFalse($encoder->isPasswordValid($user, $client->getRequest()->request->get("password")));
    }

    /**
     * @param string $email
     * @param string $password
     * @dataProvider provideInvalidEmailsAndValidPasswords
     */
    public function testInvalidEmail(string $email, string $password): void
    {
        $client = static::createClient();

        /** @var RouterInterface $router */
        $router = static::$container->get("router");
        $crawler = $client->request(Request::METHOD_GET, $router->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            "email" => $email,
            "password" => $password
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $userRepository = static::$container->get($this->userRepository);
        /** @var UserInterface $user */
        $user = $userRepository->findOneBy(["email" => $email]);
        $this->assertNull($user);
    }
}
