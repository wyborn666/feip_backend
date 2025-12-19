<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\SummerHouse;

class BookingControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private User $testUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // очищаем таблицы
        $this->entityManager->createQuery('DELETE FROM App\Entity\Booking')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\SummerHouse')->execute();

        // создаём тестового пользователя
        $this->testUser = new User();
        $this->testUser->setUsername('testuser');
        $this->testUser->setPhoneNumber('+79991234567');
        $this->testUser->setRole('ROLE_USER');
        $this->testUser->setPassword(password_hash('test123', PASSWORD_BCRYPT));

        $this->entityManager->persist($this->testUser);
        $this->entityManager->flush();

        // логиним пользователя
        $this->client->loginUser($this->testUser);
    }

    private function createTestHouse(): SummerHouse
    {
        $house = new SummerHouse();
        $house->setAddress('Test Street');
        $house->setPrice(1000);
        $house->setBedrooms(2);
        $house->setDistanceFromSea(500);
        $house->setHasShower(true);

        $this->entityManager->persist($house);
        $this->entityManager->flush();

        return $house;
    }

    public function testGetBookingNotFound(): void
    {
        $this->client->request('GET', '/booking/999999');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Booking not found', $content['error']);
    }

    public function testCreateBookingSuccessfully(): void
    {
        $house = $this->createTestHouse();

        $bookingData = [
            'phoneNumber' => $this->testUser->getPhoneNumber(),
            'houseId' => $house->getId(),
            'comment' => 'Test booking'
        ];

        $this->client->request(
            'POST',
            '/booking',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($bookingData)
        );

        $this->assertResponseIsSuccessful();

        $booking = $this->entityManager
            ->getRepository(\App\Entity\Booking::class)
            ->findOneBy(['comment' => 'Test booking']);

        $this->assertNotNull($booking);
        $this->assertEquals($this->testUser->getId(), $booking->getClient()->getId());
        $this->assertEquals($house->getId(), $booking->getHouse()->getId());
    }

    public function testCreateBookingWithMissingData(): void
    {
        $invalidData = [
            'houseId' => 1
        ];

        $this->client->request(
            'POST',
            '/booking',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($invalidData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $content = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Missing phoneNumber or houseId', $content['error']);
    }
}
