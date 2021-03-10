<?php

declare(strict_types=1);

namespace App\Tests\ResetPassword;

use App\Entity\User;
use App\Tests\RouterTestTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;

abstract class AbstractResetPasswordTest extends WebTestCase implements ResetPasswordTestInterface
{
    use RouterTestTrait;

    protected string $resetPasswordRouteName;
    protected string $forgottenPasswordRouteName;
    protected string $loginRouteName;
    protected string $userRepository;
    protected string $validEmail;
    protected string $formSelector;
    protected string $formPasswordField;

    public function testResetPasswordWithValidToken(): void
    {
        $client = static::createClient();

        $validToken = Uuid::v4();
        $newPassword = "newPassWord!!";

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::$container->get("doctrine.orm.default_entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy(["email" => $this->validEmail]);
        $user->getForgottenPassword()->setToken($validToken);
        $user->getForgottenPassword()->setRequestedAt(new DateTimeImmutable());
        $entityManager->flush();

        $crawler = $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->resetPasswordRouteName, [
            "token" => $user->getForgottenPassword()->getToken()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter($this->formSelector)->form([
            $this->formPasswordField => $newPassword
        ]);

        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        $this->assertRouteSame($this->loginRouteName);
        $user = $entityManager->getRepository(User::class)->findOneBy(["email" => $this->validEmail]);
        $this->assertNull($user->getForgottenPassword()->getToken());
        $this->assertNull($user->getForgottenPassword()->getRequestedAt());

        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = static::$container->get("security.user_password_encoder.generic");
        $this->assertTrue($encoder->isPasswordValid($user, $newPassword));
    }

    public function testResetPasswordWithInValidToken(): void
    {
        $client = static::createClient();

        $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->resetPasswordRouteName, [
            "token" => Uuid::v4()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame($this->forgottenPasswordRouteName);
    }

    public function testResetPasswordWithExpiredToken(): void
    {
        $client = static::createClient();

        $validToken = Uuid::v4();
        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::$container->get("doctrine.orm.default_entity_manager");

        $user = $entityManager->getRepository(User::class)->findOneBy(["email" => $this->validEmail]);
        $user->getForgottenPassword()->setToken($validToken);
        $user->getForgottenPassword()->setRequestedAt(new DateTimeImmutable("today"));
        $entityManager->flush();

        $client->request(Request::METHOD_GET, $this->getRouter()->generate($this->resetPasswordRouteName, [
            "token" => $user->getForgottenPassword()->getToken()
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertRouteSame($this->forgottenPasswordRouteName);
    }
}
