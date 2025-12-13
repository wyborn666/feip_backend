<?php

namespace App\Controller;
use App\Dto\BookingDto;
use App\Repository\BookingRepository;
use App\Services\BookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    public function __construct(private BookingRepository $Repository,private BookingService $bookingService) {
    }
    #[Route('/booking/{id}', name: 'app_booking', methods: ['GET'])]
    public function index(int $id): Response
    {
        $booking = $this->Repository->find($id);
        if (!$booking) {
        return new JsonResponse(['error' => 'Booking not found'], Response::HTTP_NOT_FOUND);
    }
        return new JsonResponse([
            'id' => $booking->getId(),
            'houseId' => $booking->getHouse()?->getId(),
            'phoneNumber' => $booking->getClient()?->getPhoneNumber(),
            'comment' => $booking->getComment(),
        ], Response::HTTP_OK);
    }
    #[Route('/booking', name: 'app_create_booking', methods:['POST'])]
    public function createBooking(Request $request): Response {
        $values = $request->toArray();

        if (empty($values['phoneNumber']) || empty($values['houseId'])) {
            return new JsonResponse(
                ['error' => 'Missing phoneNumber or houseId'],
                Response::HTTP_BAD_REQUEST
            );
        }
        $booking = new BookingDto(
            $values["phoneNumber"],
            $values["houseId"],
            $values["comment"] ?? "",
        );
        try {
            $this->bookingService->createBooking($booking);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_CONFLICT
            );
        }
        return new JsonResponse(
            ['status' => 'Booking created successfully'],
            Response::HTTP_CREATED
        );
    }

}