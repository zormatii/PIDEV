<?php

namespace App\Controller;
use Knp\Snappy\Pdf;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use Twig\Environment;

#[Route('/formation')]
final class FormationController extends AbstractController
{
    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_formation_show', methods: ['GET'])]
    public function show(int $id, FormationRepository $formationRepository): Response
    {
        $formation = $formationRepository->find($id);
    
        if (!$formation) {
            throw $this->createNotFoundException("La formation avec l'ID $id n'existe pas.");
        }
    
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }
    

    #[Route('/{id}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search', name: 'app_formation_search', methods: ['GET'])]
public function search(Request $request, FormationRepository $formationRepository): Response
{
    $query = $request->query->get('q', ''); 
    $formations = $formationRepository->searchFormations($query);

    return $this->render('formation/index.html.twig', [
        'formations' => $formations,
        'query' => $query,
    ]);
}


#[Route('/tri/{order}', name: 'app_formation_trier', methods: ['GET'])]
public function trier(FormationRepository $formationRepository, string $order = 'ASC'): Response
{
    $order = strtoupper($order); // Assure que c'est bien "ASC" ou "DESC"
    if (!in_array($order, ['ASC', 'DESC'])) {
        throw $this->createNotFoundException("Ordre de tri invalide !");
    }

    $formations = $formationRepository->findSortedByDate($order);

    return $this->render('formation/index.html.twig', [
        'formations' => $formations,
    ]);
}


#[Route('/formation/pdf', name: 'app_formation_pdf', methods: ['GET'])]
public function exportPdf(Pdf $pdf, FormationRepository $formationRepository, Environment $twig): Response
{
    $formations = $formationRepository->findAll();

    $html = $twig->render('formation/pdf.html.twig', [
        'formations' => $formations,
    ]);

    return new Response(
        $pdf->getOutputFromHtml($html),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="formations.pdf"',
        ]
    );
}
     
#[Route('/api/formations', name: 'api_formations')]
public function getFormations(ManagerRegistry $doctrine): JsonResponse
{
    $formations = $doctrine->getRepository(Formation::class)->findAll();

    $data = [];

    foreach ($formations as $formation) {
        $data[] = [
            'id' => $formation->getId(),
            'title' => $formation->getTitre(),
            'start' => $formation->getDatedebut()->format('Y-m-d'), // Supprime l'heure
            'end' => $formation->getDatefin() ? $formation->getDatefin()->format('Y-m-d') : null, // Supprime l'heure
        ];
    }

    return new JsonResponse($data);
}



#[Route('/calendar', name: 'calendar')]
public function calendar(): Response
{
    return $this->render('formation/calender.html.twig');
}

#[Route('/api/formations/{id}/delete', name: 'delete_formation', methods: ['DELETE'])]
public function deleteFormation(int $id, ManagerRegistry $doctrine): JsonResponse
{
    $entityManager = $doctrine->getManager();
    $formation = $doctrine->getRepository(Formation::class)->find($id);

    if (!$formation) {
        return new JsonResponse(['success' => false, 'message' => 'Formation non trouvée'], 404);
    }

    $entityManager->remove($formation);
    $entityManager->flush();

    return new JsonResponse(['success' => true, 'message' => 'Formation supprimée']);
}

#[Route('/api/formations/{id}/edit', name: 'api_formation_edit', methods: ['POST'])]
public function editFormation(Request $request, Formation $formation, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (isset($data['start'])) {
        try {
            $formation->setDatedebut(new \DateTime($data['start']));
            if (isset($data['end'])) {
                $formation->setDatefin(new \DateTime($data['end']));
            }
            $entityManager->persist($formation);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Formation mise à jour !']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Format de date invalide.'], 400);
        }
    }

    return new JsonResponse(['success' => false, 'message' => 'Données invalides.'], 400);
}



}
