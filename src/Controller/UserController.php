<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
public function index(Request $request, UserRepository $userRepository): Response
{
    $searchTerm = $request->query->get('q'); // Get the search term from the query string

    if ($searchTerm) {
        $users = $userRepository->findBySearchTerm($searchTerm); // Use the custom repository method
    } else {
        $users = $userRepository->findAll(); // Default behavior if no search term
    }

    return $this->render('user/index.html.twig', [
        'users' => $users,
    ]);
}

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle file upload
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $extension = $imageFile->guessExtension() ?: 'jpg';
            $newFilename = strtolower(str_replace(' ', '_', $user->getPrenom() . '_' . $user->getNom())) . '.' . $extension;

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
                $user->setImage($newFilename);
            } catch (FileException $e) {
                $this->addFlash('danger', 'File upload failed: ' . $e->getMessage());
                return $this->redirectToRoute('app_user_new');
            }
        }

        // Hash the password
        $plainPassword = $form->get('password')->getData();
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Save the user
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('tables', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('user/new.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        UserPasswordHasherInterface $passwordHasher // Add this parameter
    ): Response
    {
        $oldImage = $user->getImage();
    
        $form = $this->createForm(UserType::class, $user, [
            'method' => 'POST',
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle password update
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                // Hash the new password
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
    
            // Handle file upload
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Generate a unique filename for the uploaded image
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
                // Move the uploaded file to the images directory
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'File upload failed: ' . $e->getMessage());
                    return $this->redirectToRoute('app_user_edit', ['id' => $user->getId()]);
                }
    
                // Delete the old image if it exists
                if ($oldImage) {
                    $oldImagePath = $this->getParameter('images_directory') . '/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
    
                // Update the User entity with the new image filename
                $user->setImage($newFilename);
            }
    
            // Save the updated user entity
            $entityManager->flush();
    
            return $this->redirectToRoute('tables');
        }
    
        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    

    #[Route('{id}/delete', name: 'app_user_delete')]
public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
{
    
    if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
        $image = $user->getImage(); 
        if ($image) {
            $filesystem = new Filesystem();
            $imagePath = $this->getParameter('images_directory') . '/' . $image;
            if ($filesystem->exists($imagePath)) {
                $filesystem->remove($imagePath); 
            }
        }
        $entityManager->remove($user);
        $entityManager->flush();
    }
    return $this->redirectToRoute('tables', [], Response::HTTP_SEE_OTHER);
}

    #[Route('/tables', name: 'app_user_tables', methods: ['GET'])]
    public function tables(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('back/tables.html.twig', [
            'users' => $users,
        ]);
    }
}