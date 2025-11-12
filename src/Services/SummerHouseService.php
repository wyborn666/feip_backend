<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\SummerHouseDto;
use App\Entity\SummerHouse;
use Doctrine\ORM\EntityManagerInterface;

final class SummerHouseService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createHouse(SummerHouseDto $dto)
    {
        $house = new SummerHouse();
        $house->setAddress($dto->address);
        $house->setPrice($dto->price);
        $house->setBedrooms($dto->bedrooms);
        $house->setDistanceFromSea($dto->distanceFromSea);
        $house->setHasShower($dto->hasShower);


        $this->entityManager->persist($house);
        $this->entityManager->flush();
    }
}
