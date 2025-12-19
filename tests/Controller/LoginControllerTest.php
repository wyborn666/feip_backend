<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginControllerTest extends WebTestCase
{
    private function createUser(string $username, string $phone, string $password, string $role = 'ROLE_USER'): User
    {
        $container = static::getContainer();
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setUsername($username);
        $user->setPhoneNumber($phone);
        $user->setRole($role);
        $user->setPassword($passwordHasher->hashPassword($user, $password));

        $em = $container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testLoginPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('input[name="_username"]');
        self::assertSelectorExists('input[name="_password"]');
    }

    public function testLoginFailsWithWrongCredentials(): void
    {
        $client = static::createClient();

        $client->request('POST', '/login', [
            '_username' => 'wrong',
            '_password' => 'wrong',
        ]);

        self::assertResponseRedirects('/login');

        $client->followRedirect();
        self::assertSelectorExists('.alert-danger');
    }

    public function testLogoutRedirects(): void
    {
        $client = static::createClient();

        $client->request('GET', '/logout');

        self::assertResponseRedirects();
    }
}
