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
            $manager->generateTicket($booking);
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

            try{
                $token = $request->get('stripeToken');
                $request->getSession()->set('test', 'test');
                Stripe::setApiKey($this->getParameter('stripe_secret_key'));
                Charge::create(array(
                    "amount" => $total * 100,
                    "currency" => "eur",
                    "source" => $token,
                    "description" => "First test charge!"
                ));
                // Entrer en bdd
                dump($booking);
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
            catch(\Stripe\Error\Card $e) {
                // Since it's a decline, \Stripe\Error\Card will be caught
                $body = $e->getJsonBody();
                $err  = $body['error'];

                print('Status is:' . $e->getHttpStatus() . "\n");
                print('Type is:' . $err['type'] . "\n");
                print('Code is:' . $err['code'] . "\n");
                // param is '' in this case
                print('Param is:' . $err['param'] . "\n");
                print('Message is:' . $err['message'] . "\n");
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
            } catch (Exception $e) {
                // Something else happened, completely unrelated to Stripe
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