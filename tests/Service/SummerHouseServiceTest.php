<?php

namespace App\Tests\Service;

use App\Dto\SummerHouseDto;
use App\Entity\SummerHouse;
use App\Services\SummerHouseService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class SummerHouseServiceTest extends TestCase
{
    private $entityManager;
    private SummerHouseService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->service = new SummerHouseService($this->entityManager);
    }

    public function testCreateHouseSuccessfully(): void
    {
        $dto = new SummerHouseDto(
            '123 drive',
            250000,
            3,
            500,
            true
        );

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($house) use ($dto) {
                return $house instanceof SummerHouse
                    && $house->getAddress() === $dto->address
                    && $house->getPrice() === $dto->price
                    && $house->getBedrooms() === $dto->bedrooms
                    && $house->getDistanceFromSea() === $dto->distanceFromSea
                    && $house->HasShower() === $dto->hasShower;
            }));

        $this->entityManager->expects($this->once())->method('flush');

        $this->service->createHouse($dto);
    }
}
