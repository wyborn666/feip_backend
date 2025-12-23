<?php

namespace App\Controller;

use App\Dto\CreateUserDto;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user', methods: ['GET'])]
    public function index(int $id): Response
    {
        $user = $this->Repository->find($id);

        if (!$user) {
        return new JsonResponse(
            ['error' => 'User not found'],
            Response::HTTP_NOT_FOUND
        );
    }
        return new JsonResponse($user->getPhoneNumber());
    }
    public function __construct(private UserRepository $Repository,private UserService $userService) {

    }
    #[Route('/user', name: 'app_create_user', methods:['POST'])]
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response {
        $values = $request->toArray();

        if (empty($values['username']) || empty($values['phoneNumber']) || empty($values['role']) || empty($values['password'])) {
        return new JsonResponse(['error' => 'Missing data'], Response::HTTP_BAD_REQUEST);
    }
        $user = new CreateUserDto(
            $values["username"],
            $values["phoneNumber"],
            $values["role"],
            $values["password"]
        );
        try {
        $this->userService->createUser($user, $passwordHasher);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
    }
    return new JsonResponse(200);
    }

}