<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\BookingDto;
use App\Entity\Booking;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SummerHouseRepository;

final class BookingService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SummerHouseRepository $summerHouseRepository,
        private UserRepository $userRepository
    ) {
    }

    public function createBooking(BookingDto $dto): void
    {
        $house = $this->summerHouseRepository->find($dto->houseId);
        if ($house === null) {
            throw new \RuntimeException('House not found');
        }
        $user = $this->userRepository->findOneBy(['phoneNumber' => $dto->phoneNumber]);

        $booking = new Booking();
        $booking->setHouse($house);
        $booking->setComment($dto->comment);
        $booking->setClient($user);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }
}
