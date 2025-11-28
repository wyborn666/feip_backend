<?php

namespace App\Controller;

use App\Services\SummerHouseServiceCSV;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SummerHouseController extends AbstractController
{
    public function __construct(
        private readonly SummerHouseServiceCSV $summerHouseService
    ) {}

    #[Route('/api/houses', name: 'get_summer_houses')]
    public function index(): Response
    {   
       $houses =  $this->summerHouseService->getSummerHouses();
        return new JsonResponse($houses);
    }
    
}
