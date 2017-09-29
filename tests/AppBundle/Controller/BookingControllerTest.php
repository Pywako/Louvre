<?php
/**
 * Created by PhpStorm.
 * User: Pywako
 * Date: 29/09/2017
 * Time: 13:56
 */

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
    private $client = null;
    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
    }

    public function testHomepageIsUp()
    {
        $this->client->request('GET', '/');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        echo $this->client->getResponse()->getContent();
    }


}