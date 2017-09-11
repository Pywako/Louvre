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
    public function step2Action(Request $request)
    {
        $this->booking = $request->getSession()->get('booking');

        // Compter le nombre de formulaire ticket affichés et nombre de ticket à générer
        $this->count_form_ticket();

        // tant que le nombre de formulaire affiché ne correspond pas au nombre de ticket à générer
        while ($this->nb_ticket != $this->nb_form) {
            // gestion ajout/suppression formulaire affiché
            $this->generate_form_ticket();
        }

        $form = $this->createForm(BookingStep2Type::class, $this->booking);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Génération date de réservation
            $this->booking->setDateResa(date('Y-m-d'));

            //Génération code de réservation
            $this->booking->setCode(md5(uniqid(rand(), true)));

            //Entrée des tickets dans l'array tickets de l'objet booking
            $tickets = $this->booking->getTickets();
            foreach ($tickets as $key => $ticket) {
                dump($ticket);

                $prix = $this->generate_price($this->booking->getType(), $ticket->getDateNaissance()->getDate(), $ticket->getReduit());
                $ticket->setPrix($prix);
                $tickets[$key] = $ticket;
            }
            //Stockage en session des tickets
            $request->getSession()->set('booking', $this->booking);


            return $this->redirectToRoute('step3');
        }

        return $this->render(':booking:step2.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/step3", name="step3")
     */
    public function step3Action(Request $request)
    {
        $this->booking = $request->getSession()->get('booking');


        return $this->render(':booking:step3.html.twig', array(
            'tickets' => $this->booking->getTickets()
        ));
    }

    /**
     * @Route("/confirm", name="confirm")
     */
    public function confirmAction()
    {
        return $this->render(':booking:confirm.html.twig', array());
    }

    public function showAction($slug)
    {
        $url = $this->generateUrl(
            'homepage',
            array('slug' => 'homepage')
        );

        return $url;
    }

    public function count_form_ticket()
    {
        $this->nb_ticket = $this->booking->getNbTicket();
        $this->nb_form = $this->booking->getTickets()->count();
    }

    public function generate_form_ticket()
    {
        if ($this->nb_ticket != $this->nb_form) {
            if ($this->nb_ticket > $this->nb_form) {
                $this->booking->addTicket(new Ticket());
            } else {
                $ticket_array = $this->booking->getTickets();
                unset($ticket_array[$this->nb_form]);
            }
            $this->count_form_ticket();
            $this->generate_form_ticket();
        }
    }

    public function generate_price($type, $dateNaissance, $reduit)
    {
        $prixBebe = 0;
        $prixEnfant = 8;
        $prixReduit = 10;
        $prixSenior = 12;
        $prixStandard = 16;
        $coeficient = 0.5;
        $date = date('Y-m-d');

        // Calcul de l'age du client
        $age = $date - $dateNaissance;
        // Attribution du prix du billet en fonction de l'âge
        if ($age > 4) {
            if ($age > 12) {
                if ($age > 60) {
                    $prix = $prixSenior;
                } else {
                    $prix = $prixStandard;
                }
            } else {
                $prix = $prixEnfant;
            }
        } else {
            $prix = $prixBebe;
        }

        // tarif réduit
        if ($reduit == true) {
            $prix = $prixReduit;
        }

        if ($type == 2) {
            $prix = $prix * $coeficient;
        }

        return $prix;
    }
}