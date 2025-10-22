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
            $summerHouses[] = new SummerHouse(
                (int)$row[0],
                $row[1],
                (int)$row[2],
                (int)$row[3],
                (int)$row[4],
                (bool)$row[5],
                (bool)$row[6]
            );
        }

        fclose($file);
        return $summerHouses;



    }
}