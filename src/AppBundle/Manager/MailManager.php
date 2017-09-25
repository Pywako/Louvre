<?php

namespace AppBundle\Manager;

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

    public function sendConfirmMessage($booking)
    {
        $template = 'Email/confirm_mail.html.twig';
        $from = 'eticket@louvremusee.com';
        $to = $booking->getEmail();
        $subject = '[Musee Louvre] E-ticket confirmation';
        $body = $this->templating->render($template, array('booking'=> $booking));
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

    }
}