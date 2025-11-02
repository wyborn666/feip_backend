<?php
namespace App\Services;

use App\Dto\CreateUserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserService {
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    public function createUser(CreateUserDto $dto) {
        $existingUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $dto->username]);

        if ($existingUser) {
           throw new \RuntimeException('User already exists');
        }
        $user = new User();
        $user->setPhonenumber($dto->phoneNumber);
        $user->setUsername($dto->username);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}