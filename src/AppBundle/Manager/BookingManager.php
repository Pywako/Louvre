<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class BookingManager
{
    private $nbTicket;
    private $nbForm;
    private $booking;
    private $request;
    private $session;
    private $em;

    public function __construct(RequestStack $requestStack, SessionInterface $session, EntityManager $entityManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $session;
        $this->em = $entityManager;
        $this->booking = $session->get('booking');
    }

    public function getBooking()
    {
        return $this->booking;
    }

    public function setBooking($booking)
    {
        $this->request->getSession()->set('booking', $booking);
        $this->booking = $booking;
    }

    public function getTotalPrice()
    {
        return $this->booking->getTotalPrice();
    }

    private function countTicketForm()
    {
        $this->nbTicket = $this->booking->getNbTicket();
        $this->nbForm = $this->booking->getTickets()->count();
    }

    public function generateTicketForm()
    {
        $this->countTicketForm();

        while ($this->nbTicket != $this->nbForm) {
            if ($this->nbTicket > $this->nbForm) {
                $this->booking->addTicket(new Ticket());
            } else {
                $ticket_array = $this->booking->getTickets();
                unset($ticket_array[$this->nbForm]);
            }
            $this->countTicketForm();
        }
    }

    public function fillingTicket()
    {
        // Génération date de réservation
        $this->booking->setDateResa(new \DateTime());

        //Génération code de réservation
        $this->booking->setCode(md5(uniqid(rand(), true)));

    }

    public function generateTicket()
    {
        $tickets = $this->booking->getTickets();
        foreach ($tickets as $key => $ticket) {
            $price = $this->generatePrice($this->booking->getType(), $ticket->getDateNaissance(), $ticket->getReduit());
            $ticket->setPrix($price);
            $tickets[$key] = $ticket;
        }
    }

    private function generatePrice($type, $dateNaissance, $reduit)
    {
        $date = new \DateTime();

        // Calcul de l'age du client
        $age = $date->diff($dateNaissance)->y;
        $prix = null;

        if ($age < 4) {
            $prix = Ticket::TARIF_BABY;
        } elseif ($age < 12) {
            $prix = Ticket::TARIF_CHILD;
        } elseif ($age < 60) {
            $prix = Ticket::TARIF_STANDARD;
        } elseif ($age >= 60) {
            $prix = Ticket::TARIF_SENIOR;
        }

        // tarif réduit
        if ($age >= 12 && $reduit == true) {
            $prix = Ticket::TARIF_HALF;
        }

        if ($type == Booking::TYPE_HALF_DAY && $age >= 4) {
            $prix = $prix * Ticket::COEFICIENT_HALF_DAY;
        }

        return $prix;
    }

    public function registerBookingInBdd()
    {
        $this->em->persist($this->booking);
        $this->em->flush();
    }

    public function emptySession()
    {
        session_destroy();
    }
}