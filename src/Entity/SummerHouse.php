<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SummerHouseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SummerHouseRepository::class)]
final class SummerHouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $bedrooms = null;

    #[ORM\Column]
    private ?int $distanceFromSea = null;

    #[ORM\Column]
    private bool $hasShower = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(?int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getDistanceFromSea(): ?int
    {
        return $this->distanceFromSea;
    }

    public function setDistanceFromSea(int $distanceFromSea): static
    {
        $this->distanceFromSea = $distanceFromSea;

        return $this;
    }

    public function hasShower(): ?bool
    {
        return $this->hasShower;
    }

    public function setHasShower(bool $hasShower): static
    {
        $this->hasShower = $hasShower;

        return $this;
    }
}
