<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(LivreRepository $livreRepository): Response
    {
        $livres = $livreRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'livres' => $livres
        ]);
    }

    #[Route('/book/{id}', name: 'book_details')]
    public function bookDetails(int $id, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);
        
        if (!$livre) {
            throw $this->createNotFoundException('Le livre demandé n\'existe pas');
        }

        return $this->render('home/book_details.html.twig', [
            'livre' => $livre
        ]);
    }
}
