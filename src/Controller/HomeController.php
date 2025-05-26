<?php

namespace App\Controller;

use App\Repository\LivreRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(LivreRepository $livreRepository, CartService $cartService): Response
    {
        $livres = $livreRepository->findAll();
        $cart = $cartService->getCart();
        // print_r($cart);
        return $this->render('home/index.html.twig', [
            'livres' => $livres,
            'cart' => $cart
        ]);
    }

    #[Route('/book/{id}', name: 'book_details')]
    public function bookDetails(int $id, LivreRepository $livreRepository, SessionInterface $session): Response
    {
        $livre = $livreRepository->find($id);

        if (!$livre) {
            throw $this->createNotFoundException('Le livre demandé n\'existe pas');
        }

        $cart = $session->get('cart', []);
        $totalQuantity = 0;
        foreach ($cart as $qty) {
            $totalQuantity += $qty;
        }

        return $this->render('home/book_details.html.twig', [
            'livre' => $livre,
            'totalQuantity' => $totalQuantity,
        ]);
    }
}
