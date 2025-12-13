<?php

namespace App\Tests\Controller;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserRepository $userRepository;
    private UserService $userService;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->userRepository = $container->get(UserRepository::class);
        $this->userService = $container->get(UserService::class);
        $this->entityManager = $container->get('doctrine.orm.entity_manager');
        
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();
    }

    public function testGetUser(): void
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setPhoneNumber('+79991234567');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        $this->client->request('GET', "/user/{$userId}");

        $this->assertResponseIsSuccessful();
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertEquals('"+79991234567"', $content);
    }

    public function testGetUserNotFound(): void
    {
        $this->client->request('GET', '/user/999999');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testCreateUser(): void
    {
        $userData = [
            'username' => 'newuser',
            'phoneNumber' => '+79998887766'
        ];

        $this->client->request(
            'POST',
            '/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertResponseIsSuccessful();

        $user = $this->userRepository->findOneBy(['username' => 'newuser']);
        $this->assertNotNull($user);
        $this->assertEquals('+79998887766', $user->getPhoneNumber());
    }

    public function testCreateUserWithInvalidData(): void
    {
        $invalidData = ['username' => 'incompleteuser'];

        $this->client->request(
            'POST',
            '/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function testCreateUserWithDuplicateUsername(): void
    {
        $userData = [
            'username' => 'duplicateuser',
            'phoneNumber' => '+79991112233'
        ];

        $this->client->request(
            'POST',
            '/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );
        $this->assertResponseIsSuccessful();

        $this->client->request(
            'POST',
            '/user',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($userData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CONFLICT);
    }
}
