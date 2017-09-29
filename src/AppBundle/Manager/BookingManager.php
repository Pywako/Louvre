<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class BookingManager
{
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

    public function generateTicketForm()
    {
        $nbTicket = $this->booking->getNbTicket();

        /**
         * @var $ticketsCollection ArrayCollection
         */
        $ticketsCollection = $this->booking->getTickets();
        while ($nbTicket != $ticketsCollection->count()) {
            if ($nbTicket > $ticketsCollection->count()) {

                $this->booking->addTicket(new Ticket());
            } else {
                $ticketsCollection->remove($ticketsCollection->last());
            }
        }
    }

    public function fillingTicket()
    {
        // Génération date de réservation
        $this->booking->setDateResa(new \DateTime());

        //Génération code de réservation
        $this->booking->setCode(md5(uniqid(rand(), true)));

    }

    public function computeTicketPrice()
    {
        $tickets = $this->booking->getTickets();
        foreach ($tickets as $key => $ticket) {
            $price = $this->generatePrice($this->booking->getType(), $ticket->getDateNaissance(), $ticket->getReduit());
            $ticket->setPrix($price);
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

        if ($age >= 12 && $reduit === true) {
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
        $this->session->invalidate();
    }
}