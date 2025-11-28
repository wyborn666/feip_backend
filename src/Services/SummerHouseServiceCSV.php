<?php
namespace App\Services;

use App\Entity\SummerHouse;

class SummerHouseServiceCSV {
    public function __construct(private string $filepath)
    {
        
    }

    public function getSummerHouses() : array {
        $summerHouses = [];
        $file = fopen($this->filepath, "r");

        while (($row = fgetcsv($file)) !== false) {
            [$id, $address, $price, $bedrooms, $distanceFromSea, $hasShower, $hasBathroom] = $row;
            $summerHouses[] = new SummerHouse(
                id: (int)$id,
                address: $address,
                price: (int)$price,
                bedrooms: (int)$bedrooms,
                distanceFromSea: (int)$distanceFromSea,
                hasShower: (bool)$hasShower,
                hasBathroom: (bool)$hasBathroom
            );
        }

        fclose($file);
        return $summerHouses;
    }
}