<?php

namespace App\Controller;

use App\Dto\SummerHouseDto;
use App\Repository\SummerHouseRepository;
use App\Services\SummerHouseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class SummerHouseController extends AbstractController
{
    #[Route('/house/{id}', name: 'app_house', methods: ['GET'])]
    public function index(int $id): Response
    {
        $house = $this->Repository->find($id);
        if (!$house) {
            return new JsonResponse(
                ['error' => "House not found"],
                Response::HTTP_NOT_FOUND
            );
    }
        return new JsonResponse($house->getAddress());
    }
    public function __construct(private SummerHouseRepository $Repository, private SummerHouseService $houseService) {

    }
    #[Route('/house', name: 'app_create_house', methods:['POST'])]
    public function createHouse(Request $request): Response {
        $values = $request->toArray();

        if (empty($values['address']) || empty($values['price']) || 
        empty($values['bedrooms']) || empty($values['distanceFromSea']) ||
        empty($values['hasShower'])) {
            return new JsonResponse(['error' => 'Missing data'], Response::HTTP_BAD_REQUEST);
        }
        $house = new SummerHouseDto(
            $values["address"],
            $values["price"],
            $values["bedrooms"],
            $values["distanceFromSea"],
            $values["hasShower"],
        );
        try {
        $this->houseService->createHouse($house);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_CONFLICT);
        }
        return new JsonResponse(200);
    }

}