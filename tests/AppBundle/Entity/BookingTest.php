<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use PHPUnit\Framework\TestCase;

class BookingTest extends TestCase
{
    public function testGetTotal()
    {
        $booking = new Booking();

        for($i = 0; $i < 3; $i++){
            $ticket = new Ticket();
            $ticket->setPrix(16);
            $booking = $booking->addTicket($ticket);
        }

        $this->assertEquals(48, $booking->getTotalPrice());
    }
}