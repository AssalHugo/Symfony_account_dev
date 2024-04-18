<?php

namespace App\Service;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;;

class SenderMail
{

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * @param $from
     * @param $to
     * @param $subject
     * @param $body
     * @return void
     * @throws TransportExceptionInterface
     */
    public function sendMail($from, $to, $subject, $body)
    {
        //$email = (new Email())
        //    ->from($from)
        //    ->to($to)
        //    ->subject($subject)
        //    ->text($body);

        //$this->mailer->send($email);
    }

}