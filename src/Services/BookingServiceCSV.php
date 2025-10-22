<?php

namespace App\Services;

use App\Entity\Booking;

class BookingServiceCSV {
    public function __construct(public string $filepath)
    {}

    public function createBooking(Booking $booking)
    {

        $file = fopen($this->filepath, "a");

        $booking_array = array(
            $booking->id,
            $booking->phoneNumber,  
            $booking->houseId,
            $booking->comment
        );

        fputcsv($file, $booking_array); 
        fclose($file); 
    }
    public function changeBookingComment(int $id, string $comment)
    {
        $file = fopen($this->filepath, "r");
        $booking = [];
        while (($row = fgetcsv($file)) !== false) {
            $booking[] = new Booking(
                (int)$row[0],
                $row[1],
                (int)$row[2],
                $row[3],
            );
        }

        fclose($file);

        $file = fopen($this->filepath, "w");

        foreach ($booking as $book) {
            if ($book->id === $id) {
                $book->comment = $comment;
            }
            $bookingAsArray = array(
                $book->id, $book->phoneNumber, $book->houseId, $book->comment
            );

            fputcsv($file, $bookingAsArray);
        }
        fclose($file);

    }

}