<?php
namespace App\Service;

use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalendarService
{
    public function generateICS(Reservation $reservation): StreamedResponse
    {
        $filename = 'reservation_' . $reservation->getId() . '.ics';

        // Formater la date et l'heure pour le format iCalendar
        $eventStart = $reservation->getDateReservation()->format('Ymd') . 'T' . $reservation->getHeure()->format('His') . 'Z';
        $eventEnd = date('Ymd\THis\Z', strtotime($eventStart . ' +2 hours')); // Durée de l'événement = 2h

        $summary = 'Réservation : ' . $reservation->getNomEvenement();
        $description = 'Votre réservation pour ' . $reservation->getNomEvenement();
        

        // Contenu du fichier ICS
        $icsContent = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Thrive Together//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . uniqid() . "
DTSTAMP:" . gmdate('Ymd\THis\Z') . "
DTSTART:$eventStart
DTEND:$eventEnd
SUMMARY:$summary
DESCRIPTION:$description
END:VEVENT
END:VCALENDAR";

        $response = new StreamedResponse(function () use ($icsContent) {
            echo $icsContent;
        });

        $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }
}
