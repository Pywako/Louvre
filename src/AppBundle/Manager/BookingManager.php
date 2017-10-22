<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class BookingManager
{
    private $session;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager, StripeManager $stripeManager, MailManager $mailManager)
    {
        $this->session = $session;
        $this->em = $entityManager;
        $this->stripeManager = $stripeManager;
        $this->mailManager = $mailManager;
    }

    public function createBooking()
    {
        $booking = new Booking();
        return $booking;
    }

    public function getBookingInSession()
    {
        $booking = $this->session->get('booking');
        return $booking;
    }

    public function setBookingInSession(Booking $booking)
    {
        $this->session->set('booking', $booking);
    }

    public function prepareTicketForm(Booking $booking)
    {
        $nbTicket = $booking->getNbTicket();

        /**
         * @var $ticketsCollection ArrayCollection
         */
        $ticketsCollection = $booking->getTickets();
        while ($nbTicket != $ticketsCollection->count()) {
            if ($nbTicket > $ticketsCollection->count()) {
                $booking->addTicket(new Ticket());
            } else {
                $ticketsCollection->remove($ticketsCollection->last());
            }
        }
    }

    public function validateCart($stripe_private_key, Booking $booking, $locale)
    {
        try{
            $this->stripeManager->chargeBooking($stripe_private_key, $booking->getTotalPrice());
            $this->registerBookingInBdd($booking);
            $this->mailManager->sendConfirmMessage($booking, $locale);
            $this->emptySession();
        }
        catch (\Exception $e)
        {
            throw new Exception($e);
        }

    }
    public function prepareCart(Booking $booking)
    {
        $tickets = $booking->getTickets();
        foreach ($tickets as $ticket) {
            $price = $this->computePrice($booking->getType(), $ticket->getDateNaissance(), $ticket->getReduit());
            $ticket->setPrix($price);
        }
    }

    /**
     * @param int $type
     * @param /Datetime $dateNaissance
     * @param bool $reduit
     * @return float|null
     */
    public function computePrice($type, $dateNaissance, $reduit)
    {
        $date = new \DateTime();

        // Calcul de l'age du client
        $age = $date->diff($dateNaissance)->y;
        $price = null;

        if ($age < 4) {
            $price = Ticket::TARIF_BABY;
        } elseif ($age < 12) {
            $price = Ticket::TARIF_CHILD;
        } elseif ($age < 60) {
            $price = Ticket::TARIF_STANDARD;
        } elseif ($age >= 60) {
            $price = Ticket::TARIF_SENIOR;
        }

        // tarif réduit

        if ($age >= 12 && $reduit === true) {
            $price = Ticket::TARIF_DISCOUNT;
        }

        if ($type == Booking::TYPE_HALF_DAY && $age >= 4) {
            $price = $price * Ticket::COEFICIENT_HALF_DAY;
        }
        return $price;
    }

    public function registerBookingInBdd(Booking $booking)
    {
        $booking->setDateResa(new \DateTime());

        //Génération code de réservation
        $booking->setCode(md5(uniqid(rand(), true)));

        $this->em->persist($booking);
        $this->em->flush();
    }

    public function emptySession()
    {
        $this->session->invalidate();
    }
}
