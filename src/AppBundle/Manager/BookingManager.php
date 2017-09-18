<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;


class BookingManager
{
    private $nbTicket;
    private $nbForm;
    private $booking;

    public function __construct()
    {

    }

    public function getBooking()
    {
        return $this->booking;
    }

    public function setBooking($booking)
    {
        $this->booking = $booking;
    }

    private function countTicketForm($booking)
    {
        $this->nbTicket = $booking->getNbTicket();
        $this->nbForm = $booking->getTickets()->count();
    }

    public function generateTicketForm($booking)
    {
        $this->countTicketForm($booking);

        while ($this->nbTicket != $this->nbForm) {
            if ($this->nbTicket > $this->nbForm) {
                $booking->addTicket(new Ticket());
            } else {
                $ticket_array = $booking->getTickets();
                unset($ticket_array[$this->nbForm]);
            }
            $this->countTicketForm($booking);
        }
    }


    public function fillingTicket($booking)
    {
        // Génération date de réservation
        $booking->setDateResa(new \DateTime());

        //Génération code de réservation
        $booking->setCode(md5(uniqid(rand(), true)));

    }

    public function generateTicket($request, $booking)
    {
        $total = 0.00;
        $tickets = $booking->getTickets();
        foreach ($tickets as $key => $ticket) {
            $prix = $this->generatePrice($booking->getType(), $ticket->getDateNaissance(), $ticket->getReduit());
            $ticket->setPrix($prix);
            $total += $prix;
            $tickets[$key] = $ticket;
        }
        //Stockage en session des tickets
        $request->getSession()->set('booking', $booking);
        $request->getSession()->set('total', $total);
    }

    private function generatePrice($type, $dateNaissance, $reduit)
    {
        $date = new \DateTime();

        // Calcul de l'age du client
        $age = $date->diff($dateNaissance)->y;

        // Attribution du prix du billet en fonction de l'âge
        if ($age > 4) {
            if ($age > 12) {
                if ($age > 60) {
                    $prix = Ticket::TARIF_SENIOR;
                } else {
                    $prix = Ticket::TARIF_STANDARD;
                }
            } else {
                $prix = Ticket::TARIF_CHILD;
            }
        } else {
            $prix = Ticket::TARIF_BABY;
        }

        // tarif réduit
        if ($age > 12 && $reduit == true) {
            $prix = Ticket::TARIF_HALF;
        }

        if ($type == Booking::TYPE_HALF_DAY && $age > 4) {
            $prix = $prix * Ticket::COEFICIENT_HALF_DAY;
        }

        return $prix;
    }

    public function emptySession()
    {
        session_destroy();
    }

    public function writeInBdd()
    {

    }
}