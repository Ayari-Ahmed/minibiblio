<?php

namespace App\Controller;

use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\LivreRepository;

class AppUserOrdersController extends AbstractController
{
    #[Route('/app_user_orders', name: 'app_user_orders')]
    public function index(OrderItemRepository $orderItemRepository, OrderRepository $orderRepository, LivreRepository $livreRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $orders = $orderRepository->findBy(['user' => $user]);

        $orderData = [];
        foreach ($orders as $order) {
            $orderItems = $orderItemRepository->findBy(['order_id' => $order]);
            $items = [];
            foreach ($orderItems as $orderItem) {
                $product = $orderItem->getProduct();
                $items[] = [
                    'productId' => $product ? $product->getId() : null,
                    'title' => $product ? $product->getTitle() : null,
                    'quantity' => $orderItem->getQuantity(),
                    'price' => $orderItem->getPrice(),
                ];
            }

            $orderData[] = [
                'id' => $order->getId(),
                'createdAt' => $order->getCreatedAt(),
                'status' => $order->getStatus(),
                'totalPrice' => $order->getTotalPrice(),
                'items' => $items,
            ];
        }

        return $this->render('app_user_orders/index.html.twig', [
            'orders' => $orderData,
        ]);
    }
}
