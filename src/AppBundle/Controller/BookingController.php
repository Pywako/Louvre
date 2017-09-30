<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\BookingStep1Type;
use AppBundle\Form\Type\BookingStep2Type;
use AppBundle\Manager\BookingManager;
use AppBundle\Manager\MailManager;
use AppBundle\Manager\StripeManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends Controller
{
    /**
     * @param Request $request
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render(':booking:index.html.twig', array());
    }

    /**
     * @param Request $request
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/step1", name="step1")
     */
    public function step1Action(Request $request, BookingManager $bookingManager)
    {
        $booking = $bookingManager->createOrGetBooking();
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
     * @param BookingManager $bookingManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/step2", name="step2")
     */
    public function step2Action(Request $request, BookingManager $bookingManager)
    {
        $booking = $bookingManager->createOrGetBooking();
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
    }

    /**
     * @param Request $request
     * @param BookingManager $bookingManager
     * @param MailManager $mailManager
     * @param StripeManager $stripeManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route("/step3", name="step3")
     */
    public function step3Action(Request $request, BookingManager $bookingManager, MailManager $mailManager, StripeManager $stripeManager)
    {
        $booking = $bookingManager->createOrGetBooking();

        if ($request->isMethod('POST')) {
            $stripe_secret_key = $this->getParameter('stripe_private_key');
            if ($request->get('stripeToken')) {
                try {
                    $stripeManager->chargeBooking($stripe_secret_key);
                    $bookingManager->registerBookingInBdd($booking);
                    $mailManager->sendConfirmMessage($booking);
                    $bookingManager->emptySession();
                    // Redirect to message confirmation

                    return $this->redirectToRoute('confirm');
                } catch (\Exception $e) {
                    $bookingManager->displayMessage("erreur pendant la commande, veuillez recommencer.");
                }
            }
        }

        return $this->render(':booking:step3.html.twig', array(
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'tickets' => $booking->getTickets(),
            'booking' => $booking,
            'total' => $booking->getTotalPrice()
        ));
    }

    /**
     * @Route("/confirm", name="confirm")
     */
    public function confirmAction()
    {
        $this->addFlash('sucess', 'Commande effectuée');
        return $this->render(':booking:confirm.html.twig', array());
    }
}
