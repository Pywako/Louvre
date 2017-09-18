<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use AppBundle\Form\Type\BookingStep1Type;
use AppBundle\Form\Type\BookingStep2Type;
use AppBundle\Manager\BookingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->manager = new BookingManager();
    }

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
    public function step1Action(Request $request)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingStep1Type::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set('booking', $booking);
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
        $booking = $request->getSession()->get('booking');
        $manager = $bookingManager;
        $manager->generateTicketForm($booking);

        $form = $this->createForm(BookingStep2Type::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->fillingTicket($booking);
            $manager->generateTicket($request, $booking);
            return $this->redirectToRoute('step3');
        }

        return $this->render(':booking:step2.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/step3", name="step3")
     */
    public function step3Action(Request $request, BookingManager $bookingManager)
    {
        $manager    = $bookingManager;
        $booking    = $request->getSession()->get('booking');
        $total      = $request->getSession()->get('total');

        if($request->isMethod('POST')){
            dump($request->get('stripeToken'));
            try{
                $token = $request->get('stripeToken');
                $request->getSession()->set('test', 'test');
                \Stripe\Stripe::setApiKey($this->getParameter('stripe_secret_key'));
                Charge::create(array(
                    "amount" => $total * 100,
                    "currency" => "eur",
                    "source" => $token,
                    "description" => "First test charge!"
                ));
                // Entrer en bdd
                $em = $this->getDoctrine()->getManager();
                $em->persist($booking);
                $em->flush();
                // Envoi email

                // Vider session
                $manager->emptySession();
                // Redirecto message confirmation
                $this->addFlash('success', 'Commande effectuée');
                return $this->redirectToRoute('confirm');
            }
            catch(\Exception $e){
                $this->addFlash('error', 'Il y a une erreur');
                dump($e);
            }
        }

        return $this->render(':booking:step3.html.twig', array(
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'tickets'   => $booking->getTickets(),
            'booking'   => $booking,
            'total'     => $total
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