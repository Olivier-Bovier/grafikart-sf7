<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN = 'ADMIN_USER';

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@doe.fr')
            ->setUserName('admin')
            ->setPassword($this->passwordHasher->hashPassword($user, 'admin'))
            ->setIsVerified(true)
            ->setApiToken('admin_token');

        $this->addReference(self::ADMIN, $user);

        $manager->persist($user);

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setRoles(['ROLE_USER'])
                ->setEmail("user{$i}@doe.fr")
                ->setUserName("user{$i}")
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setIsVerified(true)
                ->setApiToken("user{$i}_token");

            $this->addReference("USER" . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
