<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $password_encoder)
    {
        $this->password_encoder = $password_encoder;
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$name, $last_name, $email, $password, $api_key, $roles]) {
            $user = new User();
            $user->setName($name);
            $user->setLastName($last_name);
            $user->setEmail($email);
            $user->setPassword($this->password_encoder->encodePassword($user, $password));
            $user->setVimeoApiKey($api_key);
            $user->setRoles($roles);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getUserData(): \Generator
    {
        yield ['John', 'Wayne', 'jw@symf4.loc', 'passw', '1a1732686c73381882f2f55e199f4cf9', ['ROLE_ADMIN']];
        yield ['John', 'Wayne2', 'jw2@symf4.loc', 'passw', null, ['ROLE_ADMIN']];
        yield ['John', 'Doe', 'jd@symf4.loc', 'passw', null, ['ROLE_USER']];
        yield ['Ted', 'Bundy', 'tb@symf4.loc', 'passw', null, ['ROLE_USER']];
    }
}
