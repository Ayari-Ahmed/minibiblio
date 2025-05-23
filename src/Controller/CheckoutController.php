<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class CheckoutController extends AbstractController
{
    private $cartService;
    private $authorizationChecker;

    public function __construct(CartService $cartService, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->cartService = $cartService;
        $this->authorizationChecker = $authorizationChecker;
    }

    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }


        $cart = $this->cartService->getCart();

        if (empty($cart)) {
            $this->addFlash('warning', 'Cart is empty!');
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order();
        $order->setUser($user);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus('pending');

        $total = 0;
        foreach ($cart as $cartItem) {
            $orderItem = new OrderItem();
            $orderItem->setProduct($cartItem['product']);
            $orderItem->setQuantity($cartItem['quantity']);
            $orderItem->setPrice($cartItem['product']->getPrice());

            $total += $cartItem['quantity'] * $cartItem['product']->getPrice();

            $orderItem->setOrderId($order);
            $em->persist($orderItem);
        }

        $order->setTotalPrice($total);
        $em->persist($order);
        $em->flush();

        $this->cartService->clearCart(); // Clear cart after placing order

        return $this->redirectToRoute('order_success');
    }
}
