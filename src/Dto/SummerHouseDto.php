<?php
namespace App\Dto;


class SummerHouseDto {
    public function __construct(
        public string $address,
        public int $price,
        public int $bedrooms,
        public int $distanceFromSea,
        public bool $hasShower,
    ) {}
}