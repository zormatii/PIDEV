<?php

// src/Controller/TestController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route; // Add this line

class TestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')] // Add this attribute
    public function testEmail(MailerInterface $mailer): Response
{
    $data = []; // Initialize as an empty array
    $data['from'] = 'noreply@example.com'; // Set a default value

    $email = (new Email())
        ->from($data['from']) // Now this is safe
        ->to('test@example.com')
        ->subject('Test Email')
        ->text('This is a test email.');

    $mailer->send($email);
    return new Response('Email sent!');
}
}