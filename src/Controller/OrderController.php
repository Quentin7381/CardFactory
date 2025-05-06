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
        private readonly \Doctrine\Persistence\ManagerRegistry $doctrine
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

}