<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render(':booking:index.html.twig', array());
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/mentions", name="mentions")
     * @Method({"GET"})
     */
    public function mentionsAction()
    {
        return $this->render(':static:mentions.html.twig', array());
    }
}
