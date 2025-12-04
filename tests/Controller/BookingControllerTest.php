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

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        $this->entityManager->createQuery('DELETE FROM App\Entity\Booking')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\User')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\SummerHouse')->execute();
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
        $user = new User();
        $user->setUsername('testuser');
        $user->setPhoneNumber('+79991234567');
        $this->entityManager->persist($user);

        $house = new SummerHouse();
        $house->setAddress('Test Street');
        $house->setPrice(1000);
        $house->setBedrooms(2);
        $house->setDistanceFromSea(500);
        $house->setHasShower(true);
        $this->entityManager->persist($house);

        $this->entityManager->flush();

        $bookingData = [
            'phoneNumber' => '+79991234567',
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
        $this->assertEquals($user->getId(), $booking->getClient()->getId());
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
