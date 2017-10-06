<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 29/09/2017
 * Time: 13:56
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class BookingControllerTest extends WebTestCase
{
    private $client = null;
    private $session = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->session = new Session((new MockFileSessionStorage()));
    }

    public function testHomepageIsUp()
    {
        $this->client->request('GET', '/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testStep1IsUp()
    {
        $this->client->request('GET', '/step1');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
    }

    public function testStep1FormValidation()
    {
        $crawler = $this->client->request('GET', '/step1');

        $bookingForm = $crawler->selectButton('continuer')->form();
        $bookingForm['bookingStep1[email]'] = 'wongaliceyy@gmail.com';
        $bookingForm['bookingStep1[dateVisit]'] = 2017/12/20;
        $bookingForm['bookingStep1[nbTicket]'] = 1;
        $bookingForm['bookingStep1[type]'] = 1;
        $this->session = set('booking', $bookingForm);
        $crawler = $this->client->submit($bookingForm);
    }

    public function testStep2IsUp()
    {
        if(isset($this->session) && !empty($this->session))
        {
            $this->client->request('GET', '/step2');
            $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        }
    }

    public function testStep2FormValidation()
    {
        $crawler = $this->client->request('GET', '/step2');

        $ticketForm = $crawler->selectButton('continuer')->form();
        $ticketForm['bookingStep2[nom]'] = 'test';
        $ticketForm['bookingStep2[prenom]'] = 'unit';
        $ticketForm['bookingStep2[dateNaissance]'] = 1990/12/01;
        $ticketForm['bookingStep2[pays]'] = 'FR';
        $ticketForm['reduit'] = false;
        $this->session->get('booking')->setTickets($ticketForm);
        $crawler = $this->client->submit($ticketForm);
    }
}