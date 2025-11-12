<?php

declare(strict_types=1);

namespace App\Dto;

class UserDto
{
    public function __construct(
        public int $id,
        public string $username,
        public int $phonenumber,
    ) {
    }
}
