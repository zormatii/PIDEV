<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use App\Repository\PartenaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface; // Correct namespace
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/partenaire')]
final class PartenaireController extends AbstractController
{
    // Display all partenaires
    #[Route('/', name: 'app_partenaire_view', methods: ['GET'])]
    public function listPartenaires(PartenaireRepository $partenaireRepository): Response
    {
        return $this->render('partenaire/index.html.twig', [
            'partenaires' => $partenaireRepository->findAll(),
        ]);
    }

   
#[Route('/ajouter', name: 'app_partenaire_create', methods: ['GET', 'POST'])]
    public function createPartenaire(Request $request,  EntityManagerInterface $entityManager, MailerInterface $mailer,  UrlGeneratorInterface $urlGenerator ): Response {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $partenaire->setIdP(uniqid());

            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            $partenaire->setVerificationToken($verificationToken);
            $partenaire->setIsVerified(false);

            $entityManager->persist($partenaire);
            $entityManager->flush();

            // Generate the verification link
        $verificationUrl = $urlGenerator->generate('app_verify_partner', [
            'token' => $verificationToken,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        try {
            // Send email
            $email = (new Email())
            ->from('saifeddine.khemiri@esprit.tn')
            ->to($partenaire->getEmailP())
            ->subject('Verify Your Email - VivaCulture')
            ->html("
                <html>
                <head>
                    <style>
                        body {
                            font-family: 'Arial', sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 20px;
                        }
                        .container {
                            max-width: 600px;
                            margin: auto;
                            background: white;
                            padding: 30px;
                            border-radius: 10px;
                            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
                        }
                        .header {
                            text-align: center;
                            background-color: #007BFF;
                            color: white;
                            padding: 20px;
                            border-radius: 10px;
                        }
                        h1 {
                            margin: 0;
                            font-size: 28px;
                        }
                        h2 {
                            color: #333;
                            font-size: 22px;
                            margin-top: 20px;
                        }
                        p {
                            color: #555;
                            line-height: 1.6;
                            font-size: 16px;
                        }
                        .partner-box {
                            border: 1px solid #007BFF;
                            border-radius: 8px;
                            padding: 15px;
                            margin: 20px 0;
                            background-color: #f9f9f9;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                        }
                        .partner-box h3 {
                            margin-top: 0;
                            color: #007BFF;
                        }
                        .button {
                            background-color: #28a745;
                            color: white;
                            padding: 12px 20px;
                            text-decoration: none;
                            border-radius: 5px;
                            display: inline-block;
                            margin: 20px 0;
                            font-weight: bold;
                            transition: background-color 0.3s;
                        }
                        .button:hover {
                            background-color: #218838;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 0.9em;
                            color: #aaa;
                        }
                        .footer a {
                            color: #007BFF;
                            text-decoration: none;
                        }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h1>Welcome to VivaCulture!</h1>
                        </div>
                        <h2>Hello {$partenaire->getNomP()},</h2>
                        <p>Thank you for joining the VivaCulture community! We are thrilled to have you with us.</p>
                        <p>To complete your registration, please verify your email address by clicking the button below:</p>
                        <a href='{$verificationUrl}' class='button'>Verify Your Email</a>
                        
                        <div class='partner-box'>
                            <h3>Your Details:</h3>
                            <p><strong>Name:</strong> {$partenaire->getNomP()}</p>
                            <p><strong>Email:</strong> {$partenaire->getEmailP()}</p>
                            <p><strong>Registration ID:</strong> {$partenaire->getIdP()}</p>
                        </div>
                        
                        <p>If you have any questions, our support team is here to help you.</p>
                        <p>We look forward to seeing you at our cultural events!</p>
                        
                        <div class='footer'>
                            <p>Warm regards,</p>
                            <p>The VivaCulture Team</p>
                            <p><a href='https://yourwebsite.com'>Visit our website</a></p>
                        </div>
                    </div>
                </body>
                </html>
            ");

            $mailer->send($email);

            $this->addFlash('success', 'Verification email sent successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Email sending failed: ' . $e->getMessage());
        }

        return $this->redirectToRoute('home');
    }

    return $this->render('partenaire/new.html.twig', [
        'partenaire' => $partenaire,
        'form' => $form->createView(),
    ]);
}


    // Update an existing partenaire
    #[Route('/{id}/modifier', name: 'app_partenaire_update', methods: ['GET', 'POST'])]
    public function updatePartenaire(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Get the selected collaboration from the form
            $collaboration = $form->get('collaboration')->getData();
            
            if ($collaboration !== $partenaire->getCollaboration()) {
                // Update the collaboration if it has changed
                $partenaire->setCollaboration($collaboration);

                // Add the partenaire to the collaboration's list of partenaires
                $collaboration->addPartenaire($partenaire);  // Ensure the collaboration knows about the partenaire
            }

            // Persist the updates
            $entityManager->flush();

            return $this->redirectToRoute('app_partenaire_view');
        }

        return $this->render('partenaire/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form,
        ]);
    }

    // Delete a partenaire
    #[Route('/{id}', name: 'app_partenaire_delete', methods: ['POST'])]
    public function deletePartenaire(Request $request, Partenaire $partenaire, EntityManagerInterface $entityManager): Response
    {
        // Check if the CSRF token is valid before deleting
        if ($this->isCsrfTokenValid('delete'.$partenaire->getId(), $request->request->get('_token'))) {
            // Remove the partner entity
            $entityManager->remove($partenaire);
            $entityManager->flush();
        }

        // Redirect to the partners page after deletion
        return $this->redirectToRoute('app_partenaire_view');
    }

    // Show a single partenaire
    #[Route('/partenaire/{id}', name: 'app_partenaire_show', methods: ['GET'])]
    public function showPartenaire(Partenaire $partenaire): Response
    {
        $typeDescriptions = [
            'cross_promotion' => 'Highlight each app’s features to the respective user base. For example, the event app could promote Viva Culture’s cultural experiences, while Viva Culture could showcase the event app’s upcoming events.',
            'event_integration' => 'Seamlessly integrate event details from the event app into Viva Culture, allowing users to view, register, or interact with event information directly within the app.',
            'co_branding' => 'Collaborate on a joint event or experience that merges the functionalities of both apps. This could be a co-sponsored cultural festival or a unique social experience that combines both platforms’ strengths.',
            'ticketing_registration' => 'Enable users to register or purchase event tickets via either app, with a smooth payment integration for ease of use.',
            'exclusive_content' => 'Offer exclusive content or cultural experiences for event attendees, such as workshops or VIP access, for users who engage with both apps.',
            'data_sharing' => 'Share user engagement data (with consent) to improve targeted promotions and event recommendations, creating a personalized experience for users on both platforms.',
            'influencer_partnerships' => 'Partner with influencers or celebrities to promote both apps through joint campaigns, boosting visibility and encouraging cross-platform engagement.',
        ];
        

        // Get the description based on type_p
        $description = $typeDescriptions[$partenaire->getTypeP()] ?? 'No description available for this type.';

        return $this->render('partenaire/show.html.twig', [
            'partenaire' => $partenaire,
            'description' => $description,
        ]);
    }

    // Verify email for a partenaire
    #[Route('/verify', name: 'app_verify_partner', methods: ['GET'])]
    public function verifyPartnerEmail(Request $request, EntityManagerInterface $entityManager, PartenaireRepository $partenaireRepository): Response
    {
        $token = $request->query->get('token');
        $partenaire = $partenaireRepository->findOneBy(['verificationToken' => $token]);

        if (!$partenaire) {
            throw $this->createNotFoundException('Invalid verification token.');
        }

        // Verify the email
        $partenaire->setIsVerified(true);
        $partenaire->setVerificationToken(null);
        $entityManager->flush();

        return new Response('<h1>Email verified successfully!</h1>');
    }

    

    
}