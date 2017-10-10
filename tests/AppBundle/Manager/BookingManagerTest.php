<?php


namespace Tests\AppBundle\Manager;

use AppBundle\Entity\Ticket;
use AppBundle\Manager\BookingManager;
use AppBundle\Manager\MailManager;
use AppBundle\Manager\StripeManager;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Booking;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class BookingManagerTest extends TestCase
{
    private $bookingManager;

    public function setUp()
    {
        $session = new Session(new MockArraySessionStorage());
        $em = $this->createMock(EntityManager::class);
        $stripe = $this->createMock(StripeManager::class);
        $mail = $this->createMock(MailManager::class);
        $this->bookingManager = new BookingManager($session, $em, $stripe, $mail);

    }
    public function PriceDataProvider()
    {
        return [
            [Booking::TYPE_DAY, new \DateTime('-3 year'), false, Ticket::TARIF_BABY],
            [Booking::TYPE_DAY, new \DateTime('-4 year'), false, Ticket::TARIF_CHILD],
            [Booking::TYPE_DAY, new \DateTime('-12 year'), false, Ticket::TARIF_STANDARD],
            [Booking::TYPE_DAY, new \DateTime('-60 year'), false, Ticket::TARIF_SENIOR],
            [Booking::TYPE_DAY, new \DateTime('-15 year'), true, Ticket::TARIF_DISCOUNT],
            [Booking::TYPE_DAY, new \DateTime('-3 year'), true, Ticket::TARIF_BABY],
            [Booking::TYPE_HALF_DAY, new \DateTime('-13 year'), false, (Ticket::COEFICIENT_HALF_DAY * Ticket::TARIF_STANDARD)],
            [Booking::TYPE_HALF_DAY, new \DateTime('-11 year'), false, (Ticket::COEFICIENT_HALF_DAY * Ticket::TARIF_CHILD)],
            [Booking::TYPE_HALF_DAY, new \DateTime('-60 year'), false, (Ticket::COEFICIENT_HALF_DAY * Ticket::TARIF_SENIOR)],
        ];
    }

    /**
     * @param $type
     * @param $dateNaissance
     * @param $reduit
     * @param $expectedPrice
     * @dataProvider PriceDataProvider
     */
    public function testComputePrice($type, $dateNaissance, $reduit, $expectedPrice)
    {
        $this->assertEquals($expectedPrice, $this->bookingManager->computePrice($type, $dateNaissance, $reduit));
    }

}