<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Ticket;
use App\Service\CalendarService;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Service\EmailService;

#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository, Request $request): Response
    {
        $sortOrder = $request->query->get('order', 'ASC');

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findByDateReservation($sortOrder),
            'currentOrder' => $sortOrder,
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();

            $emailService->sendReservationConfirmation($reservation);

            $this->addFlash('success', 'Votre réservation a été enregistrée avec succès !');

            return $this->redirectToRoute('app_reservation_index');
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre réservation a été mise à jour avec succès !');

            return $this->redirectToRoute('app_reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->getPayload()->getString('_token'))) {
            $tickets = $entityManager->getRepository(Ticket::class)->findBy(['reservation' => $reservation]);
            foreach ($tickets as $ticket) {
                $entityManager->remove($ticket);
            }

            $entityManager->remove($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reservation_index');
    }

    #[Route('/{id}/calendar', name: 'app_reservation_calendar', methods: ['GET'])]
    public function downloadCalendarEvent(
        int $id,
        ReservationRepository $reservationRepository,
        CalendarService $calendarService
    ): StreamedResponse {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            throw $this->createNotFoundException("Réservation introuvable.");
        }

        return $calendarService->generateICS($reservation);
    }
    #[Route('/historique', name: 'app_reservation_historique', methods: ['GET'])]
    public function historique(ReservationRepository $reservationRepository): Response
    {
        $reservationsPassees = $reservationRepository->findPastReservations();
        
        return $this->render('reservation/historique.html.twig', [
            'reservations' => $reservationsPassees,
        ]);
    }
    
}
