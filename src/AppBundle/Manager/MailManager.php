<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Booking;
use Symfony\Component\Templating\EngineInterface;

class MailManager
{
    protected $templating;
    protected $mailer;

    public function __construct(EngineInterface $templating, \Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    public function sendConfirmMessage(Booking $booking, $locale)
    {
        if($locale == 'fr'){
            $template = 'Email/confirm_mail_fr.html.twig';
        }
        elseif ($locale == 'en')
        {
            $template = 'Email/confirm_mail_en.html.twig';
        }
        $from = 'eticket@louvremusee.com';
        $to = $booking->getEmail();
        $subject = '[Musee Louvre] E-ticket confirmation';
        $body = $this->templating->render($template, array('booking'=> $booking, 'total' => $booking->getTotalPrice()
        , 'tickets' => $booking->getTickets()));
        $this->sendMessage($from, $to, $subject, $body);

    }

    protected function sendMessage($from, $to, $subject, $body)
    {
        $mail = \Swift_Message::newInstance();

        $mail
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBody($body)
            ->setContentType('text/html');
        $this->mailer->send($mail);

        return $mail;
    }
}
