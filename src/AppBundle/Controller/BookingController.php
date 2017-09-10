<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 14/08/2017
 * Time: 17:59
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Entity\Ticket;
use AppBundle\Form\Type\BookingStep1Type;
use AppBundle\Form\Type\BookingStep2Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends Controller
{
    /**
     * @param Request $request
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render(':booking:index.html.twig', array(
        ));
    }

    /**
     * @Route("/order", name="step1")
     */
    public function step1Action(Request $request)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingStep1Type::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set( 'booking', $booking);
            return $this->redirectToRoute('step2');
        }
        return $this->render(':booking:step1.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/data", name="step2")
     */
    public function step2Action(Request $request)
    {
        $booking = $request->getSession()->get('booking');


        $nb_ticket = $booking->getNbTicket();
        // Compter le nombre de formulaire ticket affichés

        $nbForm =  $booking->getTickets()->count();
        $nbFormAGenerer = $nb_ticket - $nbForm;
        // Afficher le nombre de formulaire manquant
        if($nbFormAGenerer > 0)
        {
            for($i=0; $i<$nbFormAGenerer; $i++){
                $booking->addTicket(new Ticket());
            }
        }

        $form = $this->createForm(BookingStep2Type::class, $booking);
        dump($booking);
        return $this->render(':booking:step2.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/buy", name="step3")
     */
    public function step3Action()
    {
        return $this->render(':booking:step3.html.twig', array(
        ));
    }

    /**
     * @Route("/confirm", name="confirm")
     */
    public function confirmAction()
    {
        return $this->render(':booking:confirm.html.twig', array(

        ));
    }

    public function showAction($slug)
    {
        $url = $this->generateUrl(
            'homepage',
            array('slug' =>'homepage')
        );

        return $url;
    }
}