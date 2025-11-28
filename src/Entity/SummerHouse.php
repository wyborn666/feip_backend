<?php
namespace App\Entity;


class SummerHouse {
    public function __construct(
        public int $id,
        public string $address,
        public int $price,
        public int $bedrooms,
        public int $distanceFromSea,
        public bool $hasShower,
        public bool $hasBathroom,
    ) {}
}