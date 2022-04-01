<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName());
            $user->setName($faker->lastName());
            $user->setFirstname($faker->firstName());
            $user->setEmail($faker->email());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
            $user->setPhone($faker->phoneNumber());
            $user->setActive(true);

            $manager->persist($user);
        }

        for ($i = 0; $i < 5; $i++) {
            $admin = new User();
            $admin->setUsername($faker->userName());
            $admin->setName($faker->lastName());
            $admin->setFirstname($faker->firstName());
            $admin->setEmail($faker->email());
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
            $admin->setPhone($faker->phoneNumber());
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setActive(true);

            $manager->persist($admin);
        }

        $manager->flush();
    }
}
