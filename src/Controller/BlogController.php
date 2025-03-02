<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Form\BlogType;
use App\Repository\BlogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/blog')]
final class BlogController extends AbstractController
{
    #[Route(name: 'app_blog_index', methods: ['GET'])]
    public function index(BlogRepository $blogRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $search = $request->query->get('search', '');
        $query = $blogRepository->createQueryBuilder('b')
            ->orderBy('b.dateCreation', 'DESC');
    
        if (!empty($search)) {
            $query->andWhere('b.titre LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }








        $query = $blogRepository->findBy([], ['date_creation' => 'DESC']); // Récupération des articles triés par date

        $pagination = $paginator->paginate(
            $query, // Requête
            $request->query->getInt('page', 1), // Numéro de la page (par défaut 1)
            5 // Nombre d'éléments par page
        );

        return $this->render('blog/index.html.twig', [
            'pagination' => $pagination,
            'search' => $search,
            
        ]);
    }

    #[Route('/new', name: 'app_blog_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData(); // Correspond au champ "image" du formulaire
            
            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'), // Répertoire défini dans `services.yaml`
                        $newFilename
                    );
                    $blog->setImage($newFilename); // Stocker le nom de fichier dans la base de données
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement du fichier.');
                }
            }

            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('app_blog_index');
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_blog_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                    $blog->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Erreur lors du téléchargement du fichier.');
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_blog_show', methods: ['GET'])]
    public function show(Blog $blog): Response
    {
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    #[Route('/{id}', name: 'app_blog_delete', methods: ['POST'])]
public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
{
    // Vérifier si le token CSRF est valide
    if ($this->isCsrfTokenValid('delete' . $blog->getId(), $request->request->get('_token'))) {
        // Supprimer le blog
        $entityManager->remove($blog);
        $entityManager->flush();
        
        // Ajouter une confirmation de suppression si nécessaire
        $this->addFlash('success', 'L\'article a été supprimé avec succès.');
    }

    // Rediriger vers la liste des blogs après suppression
    return $this->redirectToRoute('app_blog_index', [], Response::HTTP_SEE_OTHER);
}


#[Route('/{id}/favori', name: 'app_blog_favori', methods: ['POST'])]
public function favori(Blog $blog, EntityManagerInterface $entityManager): Response
{
    $blog->setFavori(!$blog->isFavori()); // Toggle favoris
    $entityManager->flush();

    $this->addFlash('success', $blog->isFavori() ? 'Ajouté aux favoris ❤️' : 'Retiré des favoris 💔');

    return $this->redirectToRoute('app_blog_show', ['id' => $blog->getId()]);
}


#[Route('/like/{id}', name: 'blog_like', methods: ['POST'])]
public function like(Blog $blog, EntityManagerInterface $em): Response
{
    $blog->setLikes($blog->getLikes() + 1);
    $em->flush();

    $this->addFlash('success', 'Vous avez aimé cet article ❤️');

    return $this->redirectToRoute('app_blog_index');
}






#[Route('/blog/{id}/like', name: 'blog_like', methods: ['POST'])]
public function addLike(Blog $blog, EntityManagerInterface $em, MailerService $mailer): Response
{
    $blog->setLikes($blog->getLikes() + 1);
    $em->flush();

    // Vérification si les likes atteignent 100
    if ($blog->getLikes() == 5) {
        $mailer->sendEmail($blog->getTitre());
        $this->addFlash('success', "🎯 Notification envoyée à l'administrateur pour l'article : {$blog->getTitre()}");
    }

    return $this->redirectToRoute('app_blog_index');
}







#[Route('/test-email', name: 'test_email')]
public function testEmail(MailerInterface $mailer): Response
{
    $email = (new Email())
        ->from('amenallah2025@gmail.com')
        ->to('amenallah2025@gmail.com')
        ->subject('Test Email Symfony')
        ->text('Ceci est un test depuis Symfony avec Brevo 🚀');

    $mailer->send($email);

    return new Response("Email envoyé avec succès ✅");
}


}
