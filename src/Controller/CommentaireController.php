<?php
// src/Controller/CommentaireController.php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommentaireType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\TranslationService;
use App\Entity\Blog;
use App\Service\CommentaireFilterService;




#[Route('/commentaire')]
final class CommentaireController extends AbstractController
{
    #[Route(name: 'app_commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaireRepository->findAll(),
            
        ]);
    }
    #[Route('/new/{id}', name: 'app_commentaire_new', methods: ['GET', 'POST'])]
public function new(Request $request, Blog $blog, EntityManagerInterface $entityManager, CommentaireFilterService $filterService): Response
{
    $commentaire = new Commentaire();
    $commentaire->setBlog($blog); // Associer le commentaire à l'article

    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Filtrer le contenu du commentaire avant de l'enregistrer
        $filteredContent = $filterService->filterContent($commentaire->getContenu());
        $commentaire->setContenu($filteredContent);

        // Persister le commentaire avec le contenu filtré
        $entityManager->persist($commentaire);
        $entityManager->flush();

        return $this->redirectToRoute('app_commentaire_index');
    }

    return $this->render('commentaire/new.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form,
    ]);
}

    
    
    

    #[Route('/{id}', name: 'app_commentaire_show', methods: ['GET', 'POST'])]
   public function show(Commentaire $commentaire, Request $request, EntityManagerInterface $entityManager,TranslationService $translationService): Response
{

    $reponse = new Commentaire();
    $reponse->setParent($commentaire);
    $reponse->setDateDepublication(new \DateTime());
    $form = $this->createForm(CommentaireType::class, $reponse);
    $form->handleRequest($request);



    $lang = $request->query->get('lang', 'en'); // Langue cible

    $originalText = $commentaire->getContenu();

// Détection automatique de la langue du texte original
    $detectedLang = (preg_match('/[éèàùçêîôû]/', $originalText)) ? 'fr' : 'en';

    // Traduire uniquement si nécessaire
    if ($lang !== $detectedLang && $originalText !== null  ) {
    $translatedText = $translationService->translate($originalText, $detectedLang, $lang);
    } else {
    $translatedText = $originalText;
    }

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reponse);
        $entityManager->flush();
        return $this->redirectToRoute('app_commentaire_show', ['id' => $commentaire->getId()]);
    }

    return $this->render('commentaire/show.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form->createView(),
        'contenu' => $translatedText,
        'lang' => $lang,
    ]);
}

#[Route('/{id}/edit', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager, CommentaireFilterService $filterService): Response
{
    $form = $this->createForm(CommentaireType::class, $commentaire);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Filtrer le contenu du commentaire avant de l'enregistrer
        $filteredContent = $filterService->filterContent($commentaire->getContenu());
        $commentaire->setContenu($filteredContent);

        $entityManager->flush();

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('commentaire/edit.html.twig', [
        'commentaire' => $commentaire,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commentaire);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_commentaire_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/repondre', name: 'app_commentaire_repondre', methods: ['POST'])]
public function repondre(Request $request, Commentaire $parent, EntityManagerInterface $entityManager, CommentaireFilterService $filterService): Response
{
    $reponse = new Commentaire();
    $reponse->setParent($parent);
    $reponse->setDateDepublication(new \DateTime());

    $form = $this->createForm(CommentaireType::class, $reponse);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Filtrer le contenu de la réponse avant de l'enregistrer
        $filteredContent = $filterService->filterContent($reponse->getContenu());
        $reponse->setContenu($filteredContent);

        $entityManager->persist($reponse);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_commentaire_show', ['id' => $parent->getId()]);
}

    #[Route('/commentaire/{id}/like', name: 'app_commentaire_like', methods: ['POST'])]
    public function like(Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $commentaire->setLikes($commentaire->getLikes() + 1);
        $em->flush();

        $this->addFlash('success', 'Vous avez aimé ce commentaire 👍');

        return $this->redirectToRoute('app_commentaire_show', ['id' => $commentaire->getId()]);
    }

    #[Route('/commentaire/{id}/dislike', name: 'app_commentaire_dislike', methods: ['POST'])]
    public function dislike(Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $commentaire->setDislikes($commentaire->getDislikes() + 1);
        $em->flush();

        $this->addFlash('success', 'Vous n\'avez pas aimé ce commentaire 👎');

        return $this->redirectToRoute('app_commentaire_show', ['id' => $commentaire->getId()]);
    }
    #[Route('/{id}/favori', name: 'app_commentaire_favori', methods: ['POST'])]
    public function favori(Commentaire $commentaire, EntityManagerInterface $entityManager): Response
    {
        $commentaire->setFavori(!$commentaire->isFavori());
        $entityManager->persist($commentaire);
        $entityManager->flush();
    
        $this->addFlash('success', 'Favori mis à jour avec succès ⭐');
    
        return $this->redirectToRoute('app_commentaire_show', ['id' => $commentaire->getId()]);
    }
    
}
