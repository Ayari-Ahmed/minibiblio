<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; // Added for POST data
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LivreRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, LivreRepository $livreRepository): Response
    {
        $cart = $session->get('cart', []);

        $cartData = [];
        $total = 0;
        $totalQuantity = 0;

        foreach ($cart as $id => $quantity) {
            $livre = $livreRepository->find($id);
            if ($livre) {
                $cartData[] = [
                    'livre' => $livre,
                    'quantity' => $quantity
                ];
                $itemTotal = $livre->getPrice() * $quantity;
                $discount = $livre->getDiscount() ? $itemTotal * ($livre->getDiscount() / 100) : 0;
                $total += $itemTotal - $discount;
                $totalQuantity += $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartData,
            'total' => $total,
            'totalQuantity' => $totalQuantity,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add', methods: ['POST'])]
    public function add(
        $id,
        SessionInterface $session,
        LivreRepository $livreRepository,
        Request $request // Add the Request object
    ): Response {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        // Calculate total quantity
        $totalQuantity = 0;
        foreach ($cart as $qty) {
            $totalQuantity += $qty;
        }

        // Check if it's an AJAX request
        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'message' => 'Book added to cart successfully!',
                'totalQuantity' => $totalQuantity,
            ]);
        } else {
            // If not an AJAX request, redirect to the cart page
            return $this->redirectToRoute('app_cart');
        }
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(SessionInterface $session): Response
    {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'cart_checkout')]
    public function checkout(SessionInterface $session): Response
    {
        return $this->redirectToRoute('app_checkout');
    }

    #[Route('/cart/update/{id}', name: 'cart_update', methods: ['POST'])]
    public function update($id, Request $request, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $quantity = (int) $request->request->get('quantity', 1); // Get quantity from POST data

        if ($quantity <= 0) {
            unset($cart[$id]);
        } else {
            $cart[$id] = $quantity;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/empty', name: 'cart_empty')]
    public function empty(SessionInterface $session): Response
    {
        $session->remove('cart');

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/total', name: 'cart_total')]
    public function total(SessionInterface $session, LivreRepository $livreRepository): Response
    {
        $cart = $session->get('cart', []);
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $livre = $livreRepository->find($id);
            if ($livre) {
                $total += $livre->getPrice() * $quantity;
            }
        }

        return new Response($total);
    }

    #[Route('/cart/quantity', name: 'cart_quantity')]
    public function quantity(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $quantity = 0;

        foreach ($cart as $id => $qty) {
            $quantity += $qty;
        }

        return new Response($quantity);
    }
}
