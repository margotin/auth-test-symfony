<?php

declare(strict_types=1);

namespace App\Tests\ForgottenPassword;

use App\Repository\UserRepository;
use App\Tests\RouterTestTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;

abstract class AbstractForgottenPasswordTest extends WebTestCase implements ForgottenPasswordTestInterface
{
    use RouterTestTrait;

    protected string $formSelector;
    protected string $formEmailField;
    protected string $loginRouteName;
    protected string $userRepository;
    protected string $redirectRouteName;
    protected string $nameCsrfToken;
    protected string $idUsedWhenGeneratingTheToken;

    /**
     * @param string $email
     * @dataProvider provideValidEmails
     */
    public function testSuccessfullyForgottenPassword(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            $this->formEmailField => $email
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get($this->userRepository);
        $user = $userRepository->findOneBy(["email" => $email]);
        $this->assertNotNull($user);
        $this->assertNotNull($user->getForgottenPassword()->getToken());


        $token = $client->getRequest()->request->get("forgotten_password")["_token"];

        $this->assertTrue(
            static::$container
                ->get('security.csrf.token_manager')
                ->isTokenValid(new CsrfToken($this->idUsedWhenGeneratingTheToken, $token))
        );

        $client->followRedirect();
        $this->assertRouteSame($this->redirectRouteName);
    }

    /**
     * @param string $email
     * @dataProvider provideValidEmails
     */
    public function testInvalidCsrfToken(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            $this->nameCsrfToken => "fail",
            $this->formEmailField => $email
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $token = $client->getRequest()->get("_token");
        $this->assertFalse(
            static::$container
                ->get('security.csrf.token_manager')
                ->isTokenValid(new CsrfToken($this->idUsedWhenGeneratingTheToken, $token))
        );
    }

    /**
     * @param string $email
     * @dataProvider provideInvalidEmails
     */
    public function testInvalidEmails(string $email): void
    {
        $client = static::createClient();

        $crawler = $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->loginRouteName));

        $form = $crawler->filter($this->formSelector)->form([
            $this->formEmailField => $email
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
