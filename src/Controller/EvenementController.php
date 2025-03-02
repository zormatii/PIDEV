<?php

// EvenementController.php
namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\TypeEvenement;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Writer\PngWriter;
use App\Service\MailService;



#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenementRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('url_image')->getData();
            
            
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $evenement->setUrlImage($newFilename);
                } catch (FileException $e) {
                    // Gérer l'erreur
                }
            }

            $entityManager->persist($evenement);
            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index');
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EvenementType::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'image
            $imageFile = $form->get('url_image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'File upload failed: ' . $e->getMessage());
                    return $this->redirectToRoute('app_evenement_edit', ['id' => $evenement->getId()]);
                }

                // Supprimer l'ancienne image si elle existe
                if ($evenement->getUrlImage()) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $evenement->getUrlImage();
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                // Mettre à jour l'image
                $evenement->setUrlImage($newFilename);
            }

            // Assurer que la date de début est bien définie
            if ($evenement->getDateDebut() === null) {
                $evenement->setDateDebut(new \DateTime()); // Mettre la date actuelle
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        // Ensure CSRF token validation is done correctly
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }
    // Page admin pour voir les événements en attente
    #[Route('/admin/evenements', name: 'admin_evenements')]
    public function adminEvents(EvenementRepository $evenementRepository): Response
    {
        return $this->render('evenement/admin_list.html.twig', [
            'evenements' => $evenementRepository->findBy(['statut' => 'en attente']),
        ]);
    }

    // Action pour valider un événement
    #[Route('/admin/evenement/{id}/valider', name: 'admin_event_validate')]
    public function validateEvent(
        Evenement $evenement,
        EntityManagerInterface $entityManager,
        MailService $mailService
    ): Response {
        // Changer le statut
        $evenement->setStatut('accepté');
        $entityManager->flush();

        // Préparer le contenu du mail
        $mailContent = sprintf(
            '<p>Bonjour,</p><p>Votre événement "%s" a été <strong>accepté</strong>.</p>',
            $evenement->getTitre()
        );

        // Envoyer le mail au créateur de l'événement
        $mailService->sendEmail($evenement->getMail(), 'Votre événement a été accepté', $mailContent);

        $this->addFlash('success', 'Événement accepté !');
        return $this->redirectToRoute('admin_evenements');
    }

    // Action pour refuser un événement
    #[Route('/admin/evenement/{id}/rejeter', name: 'admin_event_reject')]
    public function rejectEvent(
        Evenement $evenement,
        EntityManagerInterface $entityManager,
        MailService $mailService
    ): Response {
        // Changer le statut
        $evenement->setStatut('refusé');
        $entityManager->flush();

        // Préparer le contenu du mail
        $mailContent = sprintf(
            '<p>Bonjour,</p><p>Votre événement "%s" a été <strong>refusé</strong>.</p>',
            $evenement->getTitre()
        );

        // Envoyer le mail au créateur de l'événement
        $mailService->sendEmail($evenement->getMail(), 'Votre événement a été refusé', $mailContent);

        $this->addFlash('danger', 'Événement refusé.');
        return $this->redirectToRoute('admin_evenements');
    }

    
    #[Route('/evenement/{id}/map', name: 'app_evenement_map')]
    public function map(Evenement $evenement): Response
    {
        return $this->render('evenement/map.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    #[Route('/evenements/type/{id}', name: 'app_evenement_by_type')]
    public function eventsByType(TypeEvenement $typeEvenement, EvenementRepository $evenementRepository): Response
    {
        // Option 1 : Utiliser la relation définie dans l'entité (Collection)
        $evenements = $typeEvenement->getEvenements();

        // Option 2 : Utiliser le repository pour filtrer par type (si vous souhaitez plus de contrôle)
        // $evenements = $evenementRepository->findBy(['typeEvenement' => $typeEvenement]);

        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'selectedType' => $typeEvenement, // On peut passer le type sélectionné pour afficher un titre, par exemple
        ]);
    }


}