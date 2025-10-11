<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Author;

final class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/author/{name}', name: 'show_author')]
    public function showAuthor(string $name): Response
    {
        return $this->render('author/show.html.twig', [
            'name' => $name,
        ]);
    }

    #[Route('/authors', name: 'list_authors')]
    public function listAuthors(): Response
    {
        $authors = [
            ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            ['id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            ['id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        return $this->render('author/list.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/details/{id}', name: 'author_details')]
    public function authorDetails(int $id): Response
    {
        $authors = [
            1 => ['id' => 1, 'picture' => '/images/Victor-Hugo.jpg', 'username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com', 'nb_books' => 100],
            2 => ['id' => 2, 'picture' => '/images/william-shakespeare.jpg', 'username' => 'William Shakespeare', 'email' => 'william.shakespeare@gmail.com', 'nb_books' => 200],
            3 => ['id' => 3, 'picture' => '/images/Taha_Hussein.jpg', 'username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300],
        ];

        $author = $authors[$id] ?? null;

        if (!$author) {
            throw $this->createNotFoundException("Auteur introuvable !");
        }

        return $this->render('author/showAuthor.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/getAll', name: 'app_get')]
    public function getAllAuthor(AuthorRepository $authRepo): Response
    {
        $authors = $authRepo->findAll();

        return $this->render('author/listauthors.html.twig', [
            'authors' => $authors
        ]);
    }

    #[Route('/addAuth', name: 'app_add')]
    public function AddAuthor(ManagerRegistry $em): Response
    {
        $auth1 = new Author();
        $auth1->setUsername('Author1');
        $auth1->setEmail('author1@esprit.tn');
        
        $auth2 = new Author();
        $auth2->setUsername('Author2');
        $auth2->setEmail('author2@esprit.tn');
        
        $em->getManager()->persist($auth1);
        $em->getManager()->persist($auth2);
        $em->getManager()->flush();
        return new Response('Authors Added');
    }

    #[Route('/author/delete/{id}', name: 'author_delete')]
    public function deleteAuthor(int $id, ManagerRegistry $doctrine, AuthorRepository $authRepo): Response
    {
        $author = $authRepo->find($id);

        if (!$author) {
            throw $this->createNotFoundException("Auteur introuvable !");
        }

        $em = $doctrine->getManager();
        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('app_get');
    }

    #[Route('/author/update/{id}', name: 'author_update')]
    public function updateAuthor(int $id, Request $request, ManagerRegistry $doctrine, AuthorRepository $authRepo): Response
    {
        $author = $authRepo->find($id);

        if (!$author) {
            throw $this->createNotFoundException("Auteur introuvable !");
        }

        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');

            $author->setUsername($username);
            $author->setEmail($email);

            $em = $doctrine->getManager();
            $em->flush();

            return $this->redirectToRoute('app_get');
        }

        return $this->render('author/update.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/authadd', name: 'author_add_form')]
    public function addAuthorForm(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $email = $request->request->get('email');

            $author = new Author();
            $author->setUsername($username);
            $author->setEmail($email);

            $em = $doctrine->getManager();
            $em->persist($author);
            $em->flush();

            return $this->redirectToRoute('app_get');
        }

        return $this->render('author/add.html.twig');
    }
}
