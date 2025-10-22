<?php
namespace App\Entity;


class Booking {
    public function __construct(
        public int $id,
        public string $phoneNumber,
        public int $houseId,
        public ?string $comment,
    ) {}
}