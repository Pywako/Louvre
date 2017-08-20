<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 14/08/2017
 * Time: 17:59
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends Controller
{
    /**
     * @param Request $request
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render(':order:index.html.twig', array(

        ));
    }

    /**
     * @Route("/commander", name="commander")
     */
    public function commanderAction(Request $request)
    {
        return $this->render(':order:commander.html.twig', array(

        ));
    }

    /**
     * @Route("/coordonne", name="coordonne")
     */
    public function coordonneAction()
    {
        return $this->render(':order:coordonne.html.twig', array(

        ));
    }

    /**
     * @Route("/paiement", name="paiement")
     */
    public function paiementAction()
    {
        return $this->render(':order:paiement.html.twig', array(

        ));
    }

    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmationAction()
    {
        return $this->render(':order:confirmation.html.twig', array(

        ));
    }
}