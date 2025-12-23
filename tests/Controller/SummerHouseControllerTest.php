<?php

namespace App\Tests\Controller;

use App\Entity\SummerHouse;
use App\Entity\User;
use App\Repository\SummerHouseRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class SummerHouseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private SummerHouseRepository $houseRepository;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->houseRepository = $container->get(SummerHouseRepository::class);
        $this->entityManager = $container->get(EntityManagerInterface::class);

        foreach ($this->houseRepository->findAll() as $house) {
            $this->entityManager->remove($house);
        }
        $this->entityManager->flush();
    }

    private function createAndLoginUser(): User
    {
        $user = new User();
        $user->setUsername('testuser');
        $user->setPhoneNumber('+79991234567');
        $user->setRole('ROLE_USER');
        $user->setPassword(password_hash('test123', PASSWORD_BCRYPT));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->client->loginUser($user);

        return $user;
    }

    public function testGetHouseReturnsAddress(): void
    {
        $this->createAndLoginUser();

        $house = new SummerHouse();
        $house->setAddress('Test Street 1');
        $house->setPrice(100);
        $house->setBedrooms(2);
        $house->setDistanceFromSea(300);
        $house->setHasShower(true);

        $this->entityManager->persist($house);
        $this->entityManager->flush();

        $this->client->request('GET', '/house/'.$house->getId());

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString('"Test Street 1"', $this->client->getResponse()->getContent());
    }

    public function testGetHouseNotFound(): void
    {
        $this->createAndLoginUser();

        $this->client->request('GET', '/house/999999');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJson($this->client->getResponse()->getContent());
    }

    public function testCreateHouseSuccessfully(): void
    {
        $this->createAndLoginUser();

        $houseData = [
            'address' => 'Beach House',
            'price' => 250,
            'bedrooms' => 3,
            'distanceFromSea' => 50,
            'hasShower' => true,
        ];

        $this->client->request(
            'POST',
            '/house',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($houseData)
        );

        $this->assertResponseIsSuccessful();

        $house = $this->houseRepository->findOneBy(['address' => 'Beach House']);
        $this->assertNotNull($house);
        $this->assertEquals(250, $house->getPrice());
    }

    public function testCreateHouseMissingData(): void
    {
        $this->createAndLoginUser();

        $invalidData = [
            'address' => 'Incomplete House',
            'price' => 150
        ];

        $this->client->request(
            'POST',
            '/house',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());
    }
}
