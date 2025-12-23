<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['user:read']]),
        new Get(normalizationContext: ['groups' => ['user:read']]),
        new Post(
            normalizationContext: ['groups' => ['user:read']],
            denormalizationContext: ['groups' => ['user:write']]
        ),
    ]
)]
class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $username = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $phoneNumber = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\Column(length: 100)]
    private string $role = 'ROLE_USER';

    #[ORM\Column(length: 255)]
    #[Groups(['user:write'])]
    private string $password;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setClient($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getClient() === $this) {
                $booking->setClient(null);
            }
        }

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function eraseCredentials(): void
    {
        return; 
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->phoneNumber;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }
        public function getRoles(): array
    {
        $roles = [];
        $roles[] = $this->role;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

}
