<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
#salma
use App\Entity\Categorie;
use App\Entity\Evenement;
//saif
use App\Entity\Collaboration;
//roua
use App\Entity\Reservation;
use App\Entity\Ticket;
//ranim
use App\Entity\Blog;
use App\Entity\Commentaire;
//layess
use App\Entity\Workshop;
use App\Entity\Formation;
use App\Repository\ReservationRepository;
use App\Entity\TypeEvenement;
use App\Repository\TypeEvenementRepository;



class DashController extends AbstractController
{
    #[Route("/dash", name: "dash_page")]
    public function dash(UserRepository $userRepository): Response
    {
        // Calculate the number of active users, admins, users, organizers, and total users
        
        $admins = $userRepository->count(['role' => 'admin']); // Ensure your User entity has a `role` field
        $normale = $userRepository->count(['role' => 'user']); // Ensure your User entity has a `role` field
        $organizers = $userRepository->count(['role' => 'organizer']); // Ensure your User entity has a `role` field
        $totalUsers = $userRepository->count([]); // Count all users

        // Pass the data to the dashboard template
        return $this->render('back/dashboard.html.twig', [
            'admins' => $admins,
            'normale' => $normale,
            'organizers' => $organizers,
            'totalUsers' => $totalUsers,
        ]);
    }   
    #[Route("/tables", name: "tables")]
    public function tables(UserRepository $userRepository , EntityManagerInterface $entitymanager): Response
    {
        // Fetch all users from the database
        $users = $userRepository->findAll();
        //salma
        $categories= $entitymanager->getRepository(Categorie::class)->findall();
        $evenements= $entitymanager->getRepository(Evenement::class)->findall();
        //saif
        $collaboration= $entitymanager->getRepository(Collaboration::class)->findall();
        //roua
        $reservations=$entitymanager->getRepository(Reservation::class)->findall();
        $tickets=$entitymanager->getRepository(ticket::class)->findall();
        //ranim
        $blogs=$entitymanager->getRepository(Blog::class)->findall();
        $commentaires=$entitymanager->getRepository(Commentaire::class)->findall();
        //layess
        $workshops = $entitymanager->getRepository(Workshop::class)->findAll();
        $formations = $entitymanager->getRepository(Formation::class)->findAll();
        $typeEvenements= $entitymanager->getRepository(TypeEvenement::class)->findall();




        // Pass the users to the template
        return $this->render('back/tables.html.twig', [
            'users' => $users,
            //salma
            'categories' => $categories,
            'evenements' => $evenements,
            //saif
            'collaborations' => $collaboration,
            //roue
            'reservations' =>$reservations,
            'tickets'=>$tickets,
            //ranim
            'blogs' =>$blogs,
            'commentaires' =>$commentaires,
            //layess
            'workshops' => $workshops,
            'formations' => $formations,
            'typeEvenements' => $typeEvenements,

        ]);
    }
    #[Route("/stats", name: "stats_page")]
public function stats(ReservationRepository $reservationRepository): Response
{
    // Nombre total de réservations
    $totalReservations = $reservationRepository->countReservations();

    // Réservations par mois
    $reservationsByMonth = $reservationRepository->countReservationsByMonth();

    // Réservations par événement
    $reservationsByEvent = $reservationRepository->countReservationsByEvent();

    return $this->render('back/stats.html.twig', [
        'totalReservations' => $totalReservations,
        'reservationsByMonth' => json_encode($reservationsByMonth), // Pour Chart.js
        'reservationsByEvent' => json_encode($reservationsByEvent), // Pour Chart.js
    ]);
}

    
}