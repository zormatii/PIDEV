<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Reservation;

class EmailService
{
    private MailerInterface $mailer;
    private UrlGeneratorInterface $router;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendReservationConfirmation(Reservation $reservation)
    {
        // Générer le lien pour ajouter l'événement au calendrier
        $calendarLink = $this->router->generate(
            'app_reservation_calendar',
            ['id' => $reservation->getId()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Formatage des dates et heures
        $dateReservation = $reservation->getDateReservation() ? $reservation->getDateReservation()->format('Y-m-d') : 'Non spécifiée';
        $heureReservation = $reservation->getHeure() ? $reservation->getHeure()->format('H:i') : 'Non spécifiée';

        // Création de l'email
        $email = (new Email())
            ->from('votre-email@gmail.com') // Remplace par ton adresse email
            ->to($reservation->getEmail())
            ->subject('✨ Votre réservation est confirmée ! 🎉')
            ->html("
                <html>
                <body style='font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; margin: 0; padding: 0; background-color: #eaf1f9;'>
                    <table role='presentation' width='100%' cellpadding='0' cellspacing='0' style='background-color: #ffffff; padding: 40px;'>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #007bff; color: #fff; border-radius: 8px 8px 0 0;'>
                                <h2 style='font-size: 24px; margin: 0;'>🎉 Félicitations ! Votre réservation est confirmée 🎉</h2>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px; text-align: center;'>
                                <p style='font-size: 18px; color: #555;'>Merci d'avoir réservé pour <strong>{$reservation->getNomEvenement()}</strong>.</p>
                                <p style='font-size: 16px; color: #555;'>Voici les détails de votre réservation :</p>
                                <div style='background-color: #f4f9fc; padding: 20px; border-radius: 10px; margin-top: 20px;'>
                                    <p style='font-size: 16px; color: #007bff;'><strong>Date :</strong> {$dateReservation}</p>
                                    <p style='font-size: 16px; color: #007bff;'><strong>Heure :</strong> {$heureReservation}</p>
                                    <p style='font-size: 16px; color: #007bff;'><strong>Nombre de tickets :</strong> {$reservation->getNombreTickets()}</p>
                                </div>
                                <p style='font-size: 16px; color: #555; margin-top: 20px;'>Ajoutez cet événement à votre calendrier en cliquant ci-dessous :</p>
                                <p style='text-align: center;'>
                                    <a href='{$calendarLink}' style='background-color: #007bff; color: white; padding: 14px 24px; text-decoration: none; font-size: 18px; border-radius: 50px; box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); transition: all 0.3s ease;'>
                                        📅 Ajouter à mon calendrier
                                    </a>
                                </p>
                                <p style='font-size: 16px; color: #555; margin-top: 30px;'>Nous sommes impatients de vous voir à cet événement. Si vous avez des questions, n'hésitez pas à nous contacter.</p>
                                <p style='font-size: 14px; color: #aaa;'>À bientôt,</p>
                                <p style='font-size: 18px; font-weight: bold; color: #007bff;'>L'équipe Viva Culture</p>
                            </td>
                        </tr>
                        <tr>
                            <td style='padding: 20px; text-align: center; background-color: #f1f1f1; color: #777; font-size: 12px; border-radius: 0 0 8px 8px;'>
                                <p>Si vous ne souhaitez plus recevoir de notifications, vous pouvez vous <a href='#' style='color: #007bff;'>désabonner ici</a>.</p>
                            </td>
                        </tr>
                    </table>
                </body>
                </html>
            ");

        // Envoi de l'email
        $this->mailer->send($email);
    }
}
