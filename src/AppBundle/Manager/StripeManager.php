<?php

namespace AppBundle\Manager;

use Stripe\Charge;
use Stripe\Stripe;
use AppBundle\Entity\Booking;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class StripeManager
{
    private $request;
    private $session;
    private $bookingManager;

    public function __construct(
        RequestStack $requestStack, SessionInterface $session, BookingManager $bookingManager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->session = $session;
        $this->bookingManager = $bookingManager;
    }

    public function chargeBooking($stripe_secret_key)
    {
        $token = $this->request->get('stripeToken');
        $this->request->getSession()->set('test', 'test');
        Stripe::setApiKey($stripe_secret_key);
        Charge::create(array(
            "amount" => $this->bookingManager->getTotalPrice() * 100,
            "currency" => "eur",
            "source" => $token,
            "description" => "Buy tickets"
        ));
    }

    public function generateStripeErrorCard($e)
    {
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err = $body['error'];

        print('Status is:' . $e->getHttpStatus() . "\n");
        print('Type is:' . $err['type'] . "\n");
        print('Code is:' . $err['code'] . "\n");
        // param is '' in this case
        print('Param is:' . $err['param'] . "\n");
        print('Message is:' . $err['message'] . "\n");
    }
}