<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $userToto = new User();
        $userToto->setEmail("toto@email.com")
            ->setPassword($this->encoder->encodePassword($userToto, "password"))
        ;

        $userTata = new User();
        $userTata->setEmail("tata@email.com")
            ->setPassword($this->encoder->encodePassword($userTata, "password"))
        ;

        $manager->persist($userToto);
        $manager->persist($userTata);

        $manager->flush();
    }
}
