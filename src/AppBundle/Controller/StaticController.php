<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 20/08/2017
 * Time: 23:01
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StaticController extends Controller
{
    /**
     * @param Request $request
     * @Route("/mentions", name="mentions")
     */
    public function mentionsAction(Request $request)
    {
        return $this->render(':static:mentions.html.twig', array());
    }
}