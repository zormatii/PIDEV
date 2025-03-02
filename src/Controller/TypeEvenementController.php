<?php

namespace App\Controller;

use App\Entity\TypeEvenement;
use App\Form\TypeEvenementType;
use App\Repository\TypeEvenementRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Snappy\Pdf;

#[Route('/type_evenement')]
class TypeEvenementController extends AbstractController
{
    #[Route('/{id}', name: 'app_type_evenement_index', methods: ['GET'])]
    public function index(int $id, TypeEvenementRepository $typeEvenementRepository, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($id);
        
        if (!$categorie) {
            throw $this->createNotFoundException("Cette catégorie n'existe pas.");
        }

        $typeEvenements = $typeEvenementRepository->findBy(['categorie' => $categorie]);

        return $this->render('type_evenement/index.html.twig', [
            'typeEvenements' => $typeEvenements,
            'categorie' => $categorie,
        ]);
    }

    #[Route('/show/{id}', name: 'app_type_evenement_show', methods: ['GET'])]
    public function show(TypeEvenement $typeEvenement): Response
    {
        return $this->render('type_evenement/show.html.twig', [
            'typeEvenement' => $typeEvenement,
        ]);
    }

    


    #[Route('/new/{id}', name: 'app_type_evenement_new', methods: ['GET', 'POST'])]
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CategorieRepository $categorieRepository): Response
    {
        $categorie = $categorieRepository->find($id);
        if (!$categorie) {
            throw $this->createNotFoundException("Catégorie non trouvée.");
        }

        $typeEvenement = new TypeEvenement();
        $typeEvenement->setCategorie($categorie);

        $form = $this->createForm(TypeEvenementType::class, $typeEvenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('url_image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                    $typeEvenement->setUrlImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->persist($typeEvenement);
            $entityManager->flush();

            $this->addFlash('success', 'Type d\'événement créé avec succès.');
            return $this->redirectToRoute('app_type_evenement_index', ['id' => $id]);
        }

        return $this->render('type_evenement/new.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypeEvenement $typeEvenement, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TypeEvenementType::class, $typeEvenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('url_image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);

                    if ($typeEvenement->getUrlImage()) {
                        $oldImagePath = $this->getParameter('images_directory') . '/' . $typeEvenement->getUrlImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    $typeEvenement->setUrlImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Type d\'événement mis à jour avec succès.');
            return $this->redirectToRoute('tables');
        }

        return $this->render('type_evenement/edit.html.twig', [
            'form' => $form->createView(),
            'typeEvenement' => $typeEvenement,
        ]);
    }

    #[Route('/{id}', name: 'app_type_evenement_delete', methods: ['POST'])]
    public function delete(Request $request, TypeEvenement $typeEvenement, EntityManagerInterface $entityManager): Response
    {
        $categorieId = $typeEvenement->getCategorie()->getId();

        if ($this->isCsrfTokenValid('delete' . $typeEvenement->getId(), $request->request->get('_token'))) {
            if ($typeEvenement->getUrlImage()) {
                $oldImagePath = $this->getParameter('images_directory') . '/' . $typeEvenement->getUrlImage();
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $entityManager->remove($typeEvenement);
            $entityManager->flush();

            $this->addFlash('success', 'Type d\'événement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_type_evenement_index', ['id' => $categorieId]);
    }

    #[Route('/type-evenement/{id}/pdf', name: 'app_type_evenement_pdf')]
    public function generatePdf(TypeEvenement $typeEvenement, Pdf $snappy): Response
    {
        // Récupération des événements liés (assurez-vous que la relation est bien chargée)
        $evenements = $typeEvenement->getEvenements();

        // Rendu du template pour le PDF
        $html = $this->renderView('pdf/type_evenement.html.twig', [
            'typeEvenement' => $typeEvenement,
            'evenements'    => $evenements,
        ]);

        // Génération du PDF à partir du HTML
        $pdfContent = $snappy->getOutputFromHtml($html, [
            // Vous pouvez définir ici des options supplémentaires pour wkhtmltopdf
            'encoding' => 'utf-8',
            // Par exemple : 'page-size' => 'A4'
        ]);

        // Retourner le PDF en réponse avec les headers appropriés
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="type_evenement_' . $typeEvenement->getId() . '.pdf"',
            ]
        );
    }

}
