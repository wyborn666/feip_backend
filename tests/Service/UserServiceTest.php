<?php

namespace App\Tests\Service;

use App\Dto\CreateUserDto;
use App\Entity\User;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityRepository;

class UserServiceTest extends TestCase
{
    private $entityManager;
    private $repository;
    private $userService;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(EntityRepository::class);

        $this->entityManager->method('getRepository')
            ->with(User::class)
            ->willReturn($this->repository);

        $this->userService = new UserService($this->entityManager);
    }

    public function testCreateUserSuccessfully(): void
    {
        $dto = new CreateUserDto('newuser', '+79991234567');

        $this->repository->method('findOneBy')->with(['username' => 'newuser'])->willReturn(null);

        $this->entityManager->expects($this->once())->method('persist')->with($this->isInstanceOf(User::class));
        $this->entityManager->expects($this->once())->method('flush');

        $this->userService->createUser($dto);
    }

    public function testCreateUserAlreadyExists(): void
    {
        $dto = new CreateUserDto('existinguser', '+79991234567');

        $existingUser = new User();
        $existingUser->setUsername('existinguser');
        $this->repository->method('findOneBy')->with(['username' => 'existinguser'])->willReturn($existingUser);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User already exists');

        $this->userService->createUser($dto);
    }
}
