<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($blogTitle)
    {
        $email = (new Email())
            ->from('noreply@blog.com')
            ->to('ranimissat@gmail.com')
            ->subject('🎯 Notification : Article Populaire !')
            ->html("<h1>L'article <strong>$blogTitle</strong> a dépassé 100 Likes ❤️!</h1>");

        $this->mailer->send($email);
    }
}
