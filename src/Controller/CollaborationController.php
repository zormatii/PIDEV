<?php

namespace App\Controller;

use App\Entity\Collaboration;
use App\Form\CollaborationType;
use App\Repository\CollaborationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

use Dompdf\Dompdf;
use Dompdf\Options; // Import the correct Options class


#[Route('/collaboration')]
final class CollaborationController extends AbstractController
{
    #[Route(name: 'app_collaboration_index', methods: ['GET'])]
    public function index(CollaborationRepository $collaborationRepository): Response
    {
        return $this->render('collaboration/index.html.twig', [
            'collaborations' => $collaborationRepository->findAll(),
            'currentField' => 'date_sig',  // Default field for sorting
            'currentOrder' => 'ASC'        // Default order
        ]);
    }

    #[Route('/new', name: 'app_collaboration_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collaboration = new Collaboration();
        $form = $this->createForm(CollaborationType::class, $collaboration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collaboration);
            $entityManager->flush();

            return $this->redirectToRoute('app_collaboration_index');
        }

        return $this->render('collaboration/new.html.twig', [
            'collaboration' => $collaboration,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_collaboration_show', methods: ['GET'])]
    public function show(int $id, CollaborationRepository $collaborationRepository): Response
    {
        $collaboration = $collaborationRepository->find($id);

        if (!$collaboration) {
            throw $this->createNotFoundException('Collaboration not found!');
        }

        return $this->render('collaboration/show.html.twig', [
            'collaboration' => $collaboration,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_collaboration_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Collaboration $collaboration, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CollaborationType::class, $collaboration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_collaboration_index');
        }

        return $this->render('collaboration/edit.html.twig', [
            'collaboration' => $collaboration,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_collaboration_delete', methods: ['POST'])]
    public function delete(Request $request, Collaboration $collaboration, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $collaboration->getId(), $request->request->get('_token'))) {
            $entityManager->remove($collaboration);
            $entityManager->flush();
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/search', name: 'app_collaboration_search', methods: ['GET'])]
    public function search(Request $request, CollaborationRepository $collaborationRepository): Response
    {
        $query = $request->query->get('q', '');
        $collaborations = $collaborationRepository->searchCollaborations($query);

        return $this->render('collaboration/index.html.twig', [
            'collaborations' => $collaborations,
            'query' => $query,
            'currentField' => 'date_sig',
            'currentOrder' => 'ASC'
        ]);
    }

    #[Route('/tri/{field}/{order}', name: 'app_collaboration_trier', methods: ['GET'])]
    public function trier(Request $request, CollaborationRepository $collaborationRepository, string $field = 'date_sig', string $order = 'ASC'): Response
    {
        $allowedFields = ['nom_c', 'date_sig', 'date_ex', 'type', 'status'];
        if (!in_array($field, $allowedFields)) {
            $field = 'date_sig';
        }

        $order = strtoupper($order);
        if (!in_array($order, ['ASC', 'DESC'])) {
            $order = 'ASC';
        }

        $collaborations = $collaborationRepository->findBySortedField($field, $order);

        return $this->render('collaboration/index.html.twig', [
            'collaborations' => $collaborations,
            'currentField' => $field,
            'currentOrder' => $order
        ]);
    }

    #[Route('/collaboration/export/pdf', name: 'app_collaboration_pdf', methods: ['GET'])]
    public function exportPdf(CollaborationRepository $collaborationRepository): Response
    {
        // Retrieve all collaborations
        $collaborations = $collaborationRepository->findAll();

        // Configure Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        // Generate the PDF HTML
        $html = $this->renderView('collaboration/pdf.html.twig', [
            'collaborations' => $collaborations
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Return the PDF response
        return new Response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="collaborations.pdf"',
        ]);
    }

   

    #[Route('/collaboration/add', name: 'add_collaboration', methods: ['POST'])]
    public function addCollaboration(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom_c'], $data['type'], $data['date_sig'], $data['date_ex'], $data['status'])) {
            return new JsonResponse(['message' => 'Incomplete data'], Response::HTTP_BAD_REQUEST);
        }

        $collaboration = new Collaboration();
        $collaboration->setNomC($data['nom_c']);
        $collaboration->setType($data['type']);
        $collaboration->setDateSig(new \DateTime($data['date_sig']));
        $collaboration->setDateEx(new \DateTime($data['date_ex']));
        $collaboration->setStatus($data['status']);

        $entityManager->persist($collaboration);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Collaboration added successfully!',
            'collaboration' => [
                'id' => $collaboration->getId(),
                'nom_c' => $collaboration->getNomC(),
                'type' => $collaboration->getType(),
                'date_sig' => $collaboration->getDateSig()->format('Y-m-d'),
                'date_ex' => $collaboration->getDateEx()->format('Y-m-d'),
                'status' => $collaboration->getStatus(),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/collaboration/update/{id}', name: 'update_collaboration', methods: ['PUT'])]
    public function updateCollaboration(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $collaboration = $entityManager->getRepository(Collaboration::class)->find($id);

        if (!$collaboration) {
            return new JsonResponse(['message' => 'Collaboration not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom_c'])) {
            $collaboration->setNomC($data['nom_c']);
        }
        if (isset($data['type'])) {
            $collaboration->setType($data['type']);
        }
        if (isset($data['date_sig'])) {
            $collaboration->setDateSig(new \DateTime($data['date_sig']));
        }
        if (isset($data['date_ex'])) {
            $collaboration->setDateEx(new \DateTime($data['date_ex']));
        }
        if (isset($data['status'])) {
            $collaboration->setStatus($data['status']);
        }

        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Collaboration updated successfully!',
            'collaboration' => [
                'id' => $collaboration->getId(),
                'nom_c' => $collaboration->getNomC(),
                'type' => $collaboration->getType(),
                'date_sig' => $collaboration->getDateSig()->format('Y-m-d'),
                'date_ex' => $collaboration->getDateEx()->format('Y-m-d'),
                'status' => $collaboration->getStatus(),
            ]
        ], Response::HTTP_OK);
    }
    #[Route('/stats', name: 'app_collaboration_stats', methods: ['GET'])]
    public function stats(CollaborationRepository $collaborationRepository): Response
    {
        $activeCount = $collaborationRepository->count(['status' => 'active']);
        $expiredCount = $collaborationRepository->count(['status' => 'expired']);
    
        return $this->render('collaboration/stats.html.twig', [
            'activeCount' => $activeCount,
            'expiredCount' => $expiredCount,
        ]);
    }
   

}
