<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Entity\Order;
use App\Form\CartType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController {

    public function __construct(
        private readonly \Doctrine\Persistence\ManagerRegistry $doctrine,
        private readonly \App\Service\OrderService $orderService,
    ) {
    }

    #[Route('/cart', name: 'app_cart')]
    public function editCart(Request $request) {
        $user = $this->getUser();
        if (!$user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You must be logged in to access this page.');
        }

        $repository = $this->doctrine->getRepository(Order::class);
        
        $order = $this->orderService->getCartByUser($user);

        $this->denyAccessUnlessGranted('VIEW', $order);

        $form = $this->createForm(CartType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('EDIT', $order);
            $order = $form->getData();
            $order->setUser($user);
            $order->setStatus('cart');
            $this->doctrine->getManager()->persist($order);
            $this->doctrine->getManager()->flush();

            // Reset form to prevent bugs
            $this->addFlash('success', 'Cart updated successfully.');
        }

        $order = $this->orderService->checkOrder($order);
        $form = $this->createForm(CartType::class, $order); // Recreate form to ensure orders changes are reflected

        return $this->render('order/cart.html.twig', [
            'form' => $form->createView(),
            'order' => $order,
            'user' => $user,
        ]);
    }

    #[Route('/cart/reset', name: 'app_cart_reset')]
    public function resetCart(Request $request) {
        $user = $this->getUser();
        if (!$user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You must be logged in to access this page.');
        }

        $this->denyAccessUnlessGranted('DELETE', Order::class);

        $repository = $this->doctrine->getRepository(Order::class);
        $order = $this->orderService->getCartByUser($user);

        $this->denyAccessUnlessGranted('EDIT', $order);

        if ($order) {
            $this->orderService->orderReset($order);
            $this->doctrine->getManager()->persist($order);
            $this->doctrine->getManager()->flush();
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/checkout', name: 'app_checkout')]
    public function checkout(){
        // Redirect to payment
        return $this->redirectToRoute('app_checkout_payment');
    }

    #[Route('/cart/checkout/payment', name: 'app_checkout_payment')]
    public function checkoutPayment(Request $request) {
        $user = $this->getUser();
        if (!$user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You must be logged in to access this page.');
        }

        // Prevent user from accessing this page if they do not own the order
        $repository = $this->doctrine->getRepository(Order::class);
        $order = $this->orderService->getCartByUser($user);    
        if (!$order) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Order not found.');
        }
        $this->denyAccessUnlessGranted('EDIT', $order);

        // Prevent user from accessing this page if their adress is not filled
        if (!$user->getAddress()) {
            $this->addFlash('error', 'You must fill your address before proceeding to payment.');
            return $this->redirectToRoute('app_cart');
        }
        
        $this->orderService->complete($order);

        return $this->redirectToRoute('app_checkout_completed');
    }

    #[Route('/cart/checkout/completed', name: 'app_checkout_completed')]
    public function checkoutCompleted() {
        return $this->render('order/checkout/completed.html.twig');
    }

    #[Route('/user/orders', name: 'app_order_history')]
    public function orderHistory() {
        $user = $this->getUser();
        if (!$user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You must be logged in to access this page.');
        }

        $repository = $this->doctrine->getRepository(Order::class);
        $orders = $repository->findBy(['user' => $user, 'status' => Order::STATUS_COMPLETED]);

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
            'user' => $user,
        ]);
    }

    #[Route('/user/orders/{id}', name: 'app_order_details')]
    public function orderDetails($id) {
        $user = $this->getUser();
        if (!$user) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'You must be logged in to access this page.');
        }

        $repository = $this->doctrine->getRepository(Order::class);
        $order = $repository->find($id);

        if (!$order || $order->getUser() !== $user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Order not found or you do not have permission to view it.');
        }

        return $this->render('order/details.html.twig', [
            'order' => $order,
            'user' => $user,
        ]);
    }
}