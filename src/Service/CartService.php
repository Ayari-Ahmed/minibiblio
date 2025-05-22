<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\LivreRepository;

class CartService
{
    private $requestStack;
    private $livreRepository;

    public function __construct(RequestStack $requestStack, LivreRepository $livreRepository)
    {
        $this->requestStack = $requestStack;
        $this->livreRepository = $livreRepository;
    }

    public function getCart(): array
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $livre = $this->livreRepository->find($id);
            $cartWithData[] = [
                'product' => $livre,
                'quantity' => $quantity,
            ];
        }

        return $cartWithData;
    }

    public function addToCart(int $productId, int $quantity = 1): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        $session->set('cart', $cart);
    }

    public function removeFromCart(int $productId): void
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        unset($cart[$productId]);

        $session->set('cart', $cart);
    }

    public function clearCart(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove('cart');
    }
}
