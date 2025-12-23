<?php
namespace App\Services;

use App\Dto\CreateUserDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService {
    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    public function createUser(CreateUserDto $dto, UserPasswordHasherInterface $passwordHasher) {
        $existingUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $dto->username]);

        if ($existingUser) {
           throw new \RuntimeException('User already exists');
        }
        $user = new User();
        $plaintextPassword = $dto->password;
        $hashedPassword = $passwordHasher->hashPassword(
            $user, 
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setPhonenumber($dto->phoneNumber);
        $user->setUsername($dto->username);
        $user->setRole($dto->role); 

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

}