<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Services\BookingServiceCSV;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    public function __construct(
        private readonly BookingServiceCSV $bookingService
    ) {}
    #[Route('api/booking', name: 'app_booking_create', methods: ["POST"])]
    public function index(Request $request): Response

    {
        if (empty($request->toArray())) {
            return new JsonResponse(["error" => "request body is empty"], 422);
        }
        $values = $request->toArray();
        $booking = new Booking(
            id: $values["id"],
            phoneNumber: $values["phoneNumber"],
            houseId: $values["houseId"],
            comment: $values["comment"],
        );
        $this->bookingService->createBooking($booking);
        return new JsonResponse(["status" =>"OK"], 201);
    }

    #[Route('api/booking', name:'app_booking_change_comment', methods: ['PATCH'])]
    public function changeBookingComment(Request $request): Response
    {
        if (empty($request->toArray())) {
            return new JsonResponse(["error" => "request body is empty"], 422);
        }
        $values = $request->toArray();
        $this->bookingService->changeBookingComment($values["id"], $values["comment"]);
        return new JsonResponse(["status" => "OK"], 201);
    }


}
