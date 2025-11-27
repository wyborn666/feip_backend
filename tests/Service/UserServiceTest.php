<?php

namespace App\Tests\Service;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserServiceTest extends TestCase
{
    private $entityManager;
    private $repository;
    private $userService;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);
        $this->passwordHasher = $this->createMock(UserPasswordHasherInterface::class);

        $this->entityManager->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->userService = new UserService($this->entityManager);
    }

    public function testCreateUserSuccessfully(): void
    {
        $dto = new CreateUserDto('newuser', '+79991234567', 'ROLE_USER', '23423qwr231');

        $this->repository->method('findOneBy')->with(['username' => 'newuser'])->willReturn(null);

        $this->passwordHasher->method('hashPassword')->willReturn('hashed_password');

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(User::class));
        $this->entityManager->expects($this->once())->method('flush');

        $this->userService->createUser($dto, $this->passwordHasher);
    }

    public function testCreateUserAlreadyExists(): void
    {
        $dto = new CreateUserDto('existinguser', '+79991234567', 'ROLE_USER', 'password123');

        $existingUser = new User();
        $existingUser->setUsername('existinguser');
        $this->repository->method('findOneBy')->with(['username' => 'existinguser'])->willReturn($existingUser);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User already exists');

        $this->userService->createUser($dto, $this->passwordHasher);
    }
}
