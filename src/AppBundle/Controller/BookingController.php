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
     * @Route("/step1", name="step1")
     */
    public function step1Action(Request $request, BookingManager $bookingManager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingStep1Type::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingManager->setBooking($booking);
            return $this->redirectToRoute('step2');
        }
        return $this->render(':booking:step1.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/step2", name="step2")
     */
    public function step2Action(Request $request, BookingManager $bookingManager)
    {
        $bookingManager->generateTicketForm();

        $form = $this->createForm(BookingStep2Type::class, $bookingManager->getBooking());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookingManager->fillingTicket();
            $bookingManager->generateTicket();
            return $this->redirectToRoute('step3');
        }

        return $this->render(':booking:step2.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/step3", name="step3")
     */
    public function step3Action(Request $request, BookingManager $bookingManager, MailManager $mailManager, StripeManager $stripeManager)
    {
        $booking = $bookingManager->getBooking();

        if ($request->isMethod('POST')) {
            try {
                $stripe_secret_key = $this->getParameter('stripe_secret_key');
                $stripeManager->chargeBooking($stripe_secret_key);

                $bookingManager->registerBookingInBdd();
                $mailManager->sendConfirmMessage($booking);

                $bookingManager->emptySession();
                // Redirect to message confirmation
                $this->addFlash('success', 'Commande effectuée');
                return $this->redirectToRoute('confirm');

            } catch (\Stripe\Error\Card $e) {
                $stripeManager->generateStripeErrorCard($e);
            } catch (\Stripe\Error\RateLimit $e) {
                // Too many requests made to the API too quickly
            } catch (\Stripe\Error\InvalidRequest $e) {
                // Invalid parameters were supplied to Stripe's API
            } catch (\Stripe\Error\Authentication $e) {
                // Authentication with Stripe's API failed
                // (maybe you changed API keys recently)
            } catch (\Stripe\Error\ApiConnection $e) {
                // Network communication with Stripe failed
            } catch (\Stripe\Error\Base $e) {
                // Display a very generic error to the user, and maybe send
                // yourself an email
            } catch (\Exception $e) {
                // Something else happened, completely unrelated to Stripe
            }
        }

        return $this->render(':booking:step3.html.twig', array(
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'tickets' => $booking->getTickets(),
            'booking' => $booking,
            'total'   => $bookingManager->getTotalPrice()
        ));
    }

    /**
     * @Route("/confirm", name="confirm")
     */
    public function confirmAction()
    {
        return $this->render(':booking:confirm.html.twig', array());
    }
}