<?php
namespace App\Services;

use App\Dto\BookingDto;
use App\Entity\Booking;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SummerHouseRepository;
class BookingService {
    public function __construct(private EntityManagerInterface $entityManager, private SummerHouseRepository $SummerHouseRepository, private UserRepository $userRepository)
    {
        
    }

    public function createBooking(BookingDto $dto) {
        $house = $this->SummerHouseRepository->find($dto->houseId);
        $user = $this->userRepository->findOneBy(['phoneNumber' => $dto->phoneNumber]);

        $booking = new Booking();
        $booking->setHouse($house);
        $booking->setComment($dto->comment);
        $booking->setClient($user);


        $this->entityManager->persist($booking);
        $this->entityManager->flush();
    }

}