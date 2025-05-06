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
        $order = $repository->findOneByUser($user);
        
        if (!$order) {
            $order = new Order();
        }

        $form = $this->createForm(CartType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $form->getData();
            $order->setUser($user);
            $order->setStatus('cart');
            $this->doctrine->getManager()->persist($order);
            $this->doctrine->getManager()->flush();
        }

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

        $repository = $this->doctrine->getRepository(Order::class);
        $order = $repository->findOneByUser($user);

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

        $repository = $this->doctrine->getRepository(Order::class);
        $order = $repository->findOneByUser($user);

        if (!$order) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'Order not found.');
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
        throw new HttpException(Response::HTTP_NOT_FOUND, 'Not implemented yet.');
    }
}