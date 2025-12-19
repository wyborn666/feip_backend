<?php

namespace App\Entity;

use App\Repository\SummerHouseRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;
#[ApiResource(
    operations: [
        new Get(
            routeName: 'app_house'
        ),
        new Post(
            routeName: 'app_create_house'
        ),
    ]
)]

#[ORM\Entity(repositoryClass: SummerHouseRepository::class)]
class SummerHouse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['summerhouse:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['summerhouse:read', 'summerhouse:write'])]
    private ?string $address = null;

    #[ORM\Column]
    #[Groups(['summerhouse:read', 'summerhouse:write'])]
    private ?int $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['summerhouse:read', 'summerhouse:write'])]
    private ?int $bedrooms = null;

    #[ORM\Column]
    #[Groups(['summerhouse:read', 'summerhouse:write'])]
    private ?int $distanceFromSea = null;

    #[ORM\Column]
    #[Groups(['summerhouse:read', 'summerhouse:write'])]
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

    public function setHasShower(?bool $hasShower): static
    {
        $this->hasShower = $hasShower;

        return $this;
    }
}
