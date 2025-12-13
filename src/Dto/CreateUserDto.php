<?php

namespace App\Dto;

class CreateUserDto {
    public function __construct(
        public readonly string $username,
        public readonly string $phoneNumber,
    )
    {}
}