<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreForm;
use App\Repository\LivreRepository;
use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Order;

#[Route('/admin/livre')]
final class LivreController extends AbstractController
{
    public function __construct(
        private readonly LivreRepository $livreRepository,
        private readonly OrderItemRepository $orderItemRepository,
        private readonly OrderRepository $orderRepository
    ) {
    }

    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    public function index(Request $request, OrderRepository $orderRepository): Response
    {
        $search = $request->query->get('search', '');
        $queryBuilder = $this->livreRepository->createQueryBuilder('l');

        if ($search) {
            $queryBuilder
                ->where('l.title LIKE :search')
                ->orWhere('l.author LIKE :search')
                ->orWhere('l.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        $livres = $queryBuilder->getQuery()->getResult();
        $orders = $orderRepository->findAll();

        if ($request->isXmlHttpRequest()) {
            return $this->render('livre/_book_list.html.twig', [
                'livres' => $livres,
                'search' => $search,
            ]);
        }

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'orders' => $orders,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreForm::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreForm::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            // Find order items associated with the book
            $orderItems = $this->orderItemRepository->findBy(['product' => $livre]);

            // Remove the order items
            foreach ($orderItems as $orderItem) {
                $entityManager->remove($orderItem);
            }

            $entityManager->remove($livre);
            $entityManager->flush();

            // Add a flash message to confirm the deletion
            $this->addFlash('success', 'Le livre a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }

        return $this->redirectToRoute('app_livre_index');
    }

    #[Route('/{id}/edit_status', name: 'app_livre_edit_status', methods: ['GET', 'POST'])]
    public function editStatus(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $order = $this->orderRepository->find($id);

         if (!$order) {
            throw $this->createNotFoundException(
                'No order found for id '.$order
            );
        }

        if ($request->isMethod('POST')) {
            $newStatus = $request->request->get('status');
            error_log('Order ID: ' . $order->getId());
            error_log('New Status: ' . $newStatus);
            if (in_array($newStatus, $statuses)) {
                $order->setStatus(strtolower($newStatus));
                $entityManager->flush();

                return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
