<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Form\Type\BookingStep1Type;
use AppBundle\Form\Type\BookingStep2Type;
use AppBundle\Manager\BookingManager;
use AppBundle\Manager\MailManager;
use AppBundle\Manager\StripeManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class BookingController extends Controller
{
    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/step1", name="step1")
     * @Method({"GET","POST"})
     */
    public function step1Action(Request $request, SessionInterface $session, BookingManager $bookingManager)
    {
        if (empty($session->get('booking'))) {
            $booking = $bookingManager->createBooking();
        } else {
            $booking = $bookingManager->getBookingInSession();
        }
        $form = $this->createForm(BookingStep1Type::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingManager->setBookingInSession($booking);
            return $this->redirectToRoute('step2');
        }
        return $this->render(':booking:step1.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/step2", name="step2")
     * @Method({"GET","POST"})
     */
    public function step2Action(Request $request, SessionInterface $session, BookingManager $bookingManager)
    {
        if (!empty($session->get('booking'))) {
            $booking = $bookingManager->getBookingInSession();
            $bookingManager->prepareTicketForm($booking);
            $form = $this->createForm(BookingStep2Type::class, $booking);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $bookingManager->prepareCart($booking);
                return $this->redirectToRoute('step3');
            }
            return $this->render(':booking:step2.html.twig', array(
                'form' => $form->createView()
            ));
        } else {
            $this->addFlash('error', 'Pas de commande en cours, veuillez recommencer');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @param BookingManager $bookingManager
     * @param MailManager $mailManager
     * @param StripeManager $stripeManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/step3", name="step3")
     * @Method({"GET","POST"})
     */
    public function step3Action(Request $request, SessionInterface $session, BookingManager $bookingManager, MailManager $mailManager, StripeManager $stripeManager)
    {
        if (!empty($session->get('booking'))) {
            $booking = $bookingManager->getBookingInSession();
            if ($request->isMethod('POST') && $request->get('stripeToken')) {
                try {
                    $stripe_private_key = $this->getParameter('stripe_private_key');
                    $bookingManager->validateCart($stripe_private_key, $booking);
                    return $this->redirectToRoute('confirm', ['code' => $booking->getCode()]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenu pendant la commande, veuillez recommencer.');
                }
            }
            return $this->render(':booking:step3.html.twig', array(
                'stripe_public_key' => $this->getParameter('stripe_public_key'),
                'tickets' => $booking->getTickets(), 'booking' => $booking, 'total' => $booking->getTotalPrice()
            ));
        } else {
            $this->addFlash('error', 'Pas de commande en cours, veuillez recommencer');
            return $this->redirectToRoute('homepage');
        }
    }

    /**
     * @Route("/confirm/{code}", name="confirm")
     * @Method({"GET"})
     */
    public function confirmAction(Booking $booking = null, $code = "default")
    {
        if (!empty($booking) && !empty($booking->getCode()) && $code === $booking->getCode()){
            $this->addFlash('sucess', 'Commande effectuée');
            return $this->render(':booking:confirm.html.twig', array('tickets' => $booking->getTickets(),
                'booking' => $booking, 'total' => $booking->getTotalPrice()));

        } else {
            $this->addFlash('error', 'Pas de commande en cours, veuillez recommencer');
            return $this->redirectToRoute('homepage');
        }
    }
}
