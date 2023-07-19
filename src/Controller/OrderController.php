<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class OrderController extends AbstractController
{
    #[Route('/orders', 'create_order', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager, OrderService $orderService): Response
    {
        $data = $request->getPayload()->all();

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order)
            ->submit($data);

        if ($form->isValid()) {
            $deliveryDate = $orderService->getEstimatedDeliveryDate($order->getDeliveryOption());
            $order->setEstimatedDeliveryDate($deliveryDate);

            $entityManager->persist($order);
            $entityManager->flush();

            $data = [
                'message' => sprintf('New Order created (%s)', $order->getId()),
                'order' => $order
            ];

            return $this->json($data, Response::HTTP_CREATED);
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = sprintf("%s (%s)", $error->getMessage(), $error->getOrigin()->getName());
        }

        return $this->json($errors, Response::HTTP_BAD_REQUEST);
    }

    #[Route('/orders', 'get_orders', methods: ['GET'])]
    public function getOrders(Request $request, OrderRepository $orderRepository, OrderService $orderService): Response
    {
        $id = $request->query->get('id');
        $status = $request->query->get('status');

        if ($id !== null) {
            $order = $orderRepository->find($id);

            if ($order instanceof Order) {
                return $this->json($order);
            }

            return $this->json(sprintf('No Order found (%s)', $id), Response::HTTP_NOT_FOUND);
        }

        if ($status !== null) {
            if ($orderService->isValidStatus($status)) {
                $orders = $orderRepository->findBy(
                    ['status' => $status],
                    ['estimatedDeliveryDate' => 'ASC']
                );

                return $this->json($orders);
            }

            return $this->json(sprintf('Status not valid (%s)', $status), Response::HTTP_BAD_REQUEST);
        }

        $orders = $orderRepository->findAll();

        return $this->json($orders);
    }

    #[Route('/orders', 'update_order', methods: ['PATCH'])]
    public function updateOrder(Request $request, EntityManagerInterface $entityManager, OrderService $orderService): Response
    {
        $id = $request->getPayload()->get('id');
        $status = $request->getPayload()->get('status');
        $order = $entityManager->getRepository(Order::class)->find($id);

        if (!$order instanceof Order) {
            return $this->json(sprintf('No Order found (%s)', $id), Response::HTTP_NOT_FOUND);
        }

        if (!$orderService->isValidStatus($status)) {
            return $this->json(sprintf('Status not valid (%s)', $status), Response::HTTP_BAD_REQUEST);
        }

        $order->setStatus($status);
        $entityManager->persist($order);
        $entityManager->flush();

        $data = [
            'message' => sprintf("Order (%s) delivery status updated to '%s'", $id, $status),
            'order' => $order
        ];

        return $this->json($data);
    }
}