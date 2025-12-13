<?php

namespace App\Tests\Service;

use App\Dto\BookingDto;
use App\Entity\Booking;
use App\Entity\SummerHouse;
use App\Entity\User;
use App\Services\BookingService;
use App\Repository\SummerHouseRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BookingServiceTest extends TestCase
{
    private $entityManager;
    private $summerHouseRepository;
    private $userRepository;
    private BookingService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->summerHouseRepository = $this->createMock(SummerHouseRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->service = new BookingService(
            $this->entityManager,
            $this->summerHouseRepository,
            $this->userRepository
        );
    }

    public function testCreateBookingSuccessfully(): void
    {
        $dto = new BookingDto('+79991234567', 1, 'Test comment');

        $user = new User();
        $house = new SummerHouse();

        $this->userRepository
            ->method('findOneBy')
            ->with(['phoneNumber' => $dto->phoneNumber])
            ->willReturn($user);

        $this->summerHouseRepository
            ->method('find')
            ->with($dto->houseId)
            ->willReturn($house);

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($booking) use ($dto, $user, $house) {
                return $booking instanceof Booking &&
                    $booking->getComment() === $dto->comment &&
                    $booking->getClient() === $user &&
                    $booking->getHouse() === $house;
            }));

        $this->entityManager->expects($this->once())->method('flush');

        $this->service->createBooking($dto);
    }
}
