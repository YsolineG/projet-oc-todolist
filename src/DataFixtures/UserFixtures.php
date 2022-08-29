<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordHasherInterface
     */
    private $hasher;

    public const USER_REFERENCE = 'user-paul';
    public const USER_ANONYMOUS = 'user-anonymous';

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('Paul');
        $user->setEmail('paul@test.com');
        $user->setRoles([User::ROLE_USER]);
        $password = $this->hasher->hashPassword($user, 'test');
        $user->setPassword($password);
        $this->addReference(self::USER_REFERENCE, $user);

        $manager->persist($user);

        $userAdmin = new User();
        $userAdmin->setUsername('Admin');
        $userAdmin->setEmail('admin@test.com');
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $password = $this->hasher->hashPassword($userAdmin, 'test');
        $userAdmin->setPassword($password);

        $manager->persist($userAdmin);

        $userAnonymous = new User();
        $userAnonymous->setUsername('Anonymous');
        $userAnonymous->setEmail('anonymous@test.com');
        $userAnonymous->setRoles(['ROLE_ANONYMOUS']);
        $password = $this->hasher->hashPassword($userAnonymous, 'test');
        $userAnonymous->setPassword($password);
        $this->addReference(self::USER_ANONYMOUS, $userAnonymous);

        $manager->persist($userAnonymous);

        $manager->flush();
    }
}