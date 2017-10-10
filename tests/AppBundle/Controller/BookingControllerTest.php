<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class BookingControllerTest extends WebTestCase
{
    /**
     * @var Client
     */
    private $client = null;
    private $session = null;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->session = new Session((new MockFileSessionStorage()));
    }

    public function UrlDataProvider()
    {
        return [
            ['/step1', Response::HTTP_OK],
            ['/step2', Response::HTTP_FOUND],
            ['/step3', Response::HTTP_FOUND],
            ['/confirm', Response::HTTP_FOUND]
        ];
    }

    /**
     * @dataProvider UrlDataProvider
     */
    public function testUrl($url, $expected)
    {
        $this->client->request('GET', $url);
        $this->assertSame($expected, $this->client->getResponse()->getStatusCode());
    }

    public function testOrderFlow()
    {
        $crawler = $this->client->request('GET', '/step1');

        $bookingForm = $crawler->selectButton('Continuer')->form();
        $bookingForm['booking_step1[email][first]'] = 'wongaliceyy@gmail.com';
        $bookingForm['booking_step1[email][second]'] = 'wongaliceyy@gmail.com';
        $bookingForm['booking_step1[dateVisit]'] = "20/12/2017";
        $bookingForm['booking_step1[nbTicket]'] = 1;
        $bookingForm['booking_step1[type]'] = 1;

        $crawler = $this->client->submit($bookingForm);

        $this->assertEquals($this->client->getResponse()->getStatusCode(), Response::HTTP_FOUND);

        $crawler = $this->client->followRedirect();

        $ticketForm = $crawler->selectButton('Continuer')->form();
        $ticketForm['booking_step2[tickets][0][nom]'] = 'test';
        $ticketForm['booking_step2[tickets][0][prenom]'] = 'unit';
        $ticketForm['booking_step2[tickets][0][dateNaissance]'] = '01/12/1990';
        $ticketForm['booking_step2[tickets][0][pays]'] = 'FR';
        $ticketForm['booking_step2[tickets][0][reduit]'] = false;

        $crawler = $this->client->submit($ticketForm);
        $this->assertEquals($this->client->getResponse()->getStatusCode(), Response::HTTP_FOUND);
    }


}