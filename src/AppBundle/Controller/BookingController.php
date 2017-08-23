<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 14/08/2017
 * Time: 17:59
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Booking;
use AppBundle\Form\Type\BookingCommanderType;
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
    public function orderAction(Request $request)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingCommanderType::class, $booking);
        return $this->render(':booking:order.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/data", name="step2")
     */
    public function dataAction()
    {
        return $this->render(':booking:data.html.twig', array(

        ));
    }

    /**
     * @Route("/buy", name="step3")
     */
    public function buyAction()
    {
        return $this->render(':booking:buy.html.twig', array(

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
}