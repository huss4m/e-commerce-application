<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $passwordEncoder, private SluggerInterface $slugger) {}


    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@admin.fr');
        $admin->setLastname('Admin');
        $admin->setFirstname('Admin');
        $admin->setAddress('Test');
        $admin->setZipCode('11111');
        $admin->setCity('Test');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);


        $faker = Faker\Factory::create('fr_FR');

        for($usr = 1; $usr <=5 ; $usr++) {
            $user = new Users();
            $user->setEmail($faker->email());
            $user->setLastname($faker->lastName());
            $user->setFirstname($faker->firstName());
            $user->setAddress($faker->streetAddress());
            $user->setZipCode($faker->postCode());
            $user->setCity($faker->city());
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );

            
            $manager->persist($admin);
        }

        $manager->flush();
    }
}
