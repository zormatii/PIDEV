<?php

// src/Controller/ApiEvenementController.php
namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiEvenementController extends AbstractController
{
    #[Route('/api/evenements', name: 'api_evenements')]
    public function getEvenements(EvenementRepository $evenementRepository): JsonResponse
    {
        $evenements = $evenementRepository->findAll();
        $data = [];

        foreach ($evenements as $event) {
            $data[] = [
                'id' => $event->getId(),
                'titre' => $event->getTitre(), // <-- Corrigé ici
                'latitude' => $event->getLatitude(),
                'longitude' => $event->getLongitude(),
                'date' => $event->getDateDebut()->format('Y-m-d H:i'), // Ajout de la date
                'url_image' => $event->getUrlImage(), // Ajout de l'URL de l'image
            ];
        }

        return new JsonResponse($data);
    }

}
