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
    var $booking;
    var $nb_ticket;
    var $nb_form;

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
     * @Route("/step1", name="step1")
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
     * @Route("/step2", name="step2")
     */
    public function step2Action(Request $request)
    {
        $this->booking = $request->getSession()->get('booking');

        // Compter le nombre de formulaire ticket affichés et nombre de ticket à générer
        $this->count_form_ticket();

        // tant que le nombre de formulaire affiché ne correspond pas au nombre de ticket à générer
        while($this->nb_ticket != $this->nb_form)
        {
            // gestion ajout/suppression formulaire affiché
            $this->generate_form_ticket();
        }

        $form = $this->createForm(BookingStep2Type::class, $this->booking);

        /*
        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set( 'booking', $booking);
            return $this->redirectToRoute('step3');
        }*/

        return $this->render(':booking:step2.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/step3", name="step3")
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
    public function count_form_ticket()
    {
        $this->nb_ticket = $this->booking->getNbTicket();
        $this->nb_form =  $this->booking->getTickets()->count();
    }

    public function generate_form_ticket()
    {
        if($this->nb_ticket != $this->nb_form) {
            if($this->nb_ticket > $this->nb_form)
            {
                $this->booking->addTicket(new Ticket());
            }
            else{
                $ticket_array = $this->booking->getTickets();
                unset($ticket_array[$this->nb_form]);
            }
            $this->count_form_ticket();
            $this->generate_form_ticket();
        }
    }
}