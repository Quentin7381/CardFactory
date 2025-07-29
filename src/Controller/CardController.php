<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Card;
use App\Form\CardType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order;
use App\Service\CardService;

final class CardController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly \App\Service\OrderService $orderService
    ) {
    }

    #[Route('/cards', name: 'app_card_list')]
    public function list(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to view this page.');
            return $this->redirectToRoute('app_login');
        }

        // Get users cards
        $cards = $this->entityManager->getRepository(Card::class)->findBy(['author' => $user]);
        // Render
        return $this->render('card/list.html.twig', [
            'cards' => $cards,
        ]);
    }

    #[Route('/cards/add', name: 'app_card_add')]
    public function add(Request $request, CardService $cardService): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'You must be logged in to view this page.');
            return $this->redirectToRoute('app_login');
        }

        $card = new Card();
        $card->setAuthor($user);

        // Get the form
        $form = $this->createForm(CardType::class, $card);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('card_image')->getData();
            $cardService->attachImageToCard($imageFile, $card);

            // Save the card
            $this->entityManager->persist($card);
            $this->entityManager->flush();

            // Redirect to the list page
            return $this->redirectToRoute('app_card_list');
        }

        // Render the form
        return $this->render('card/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cards/edit/{id}', name: 'app_card_edit')]
    public function edit(Request $request, Card $card, CardService $cardService): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('EDIT', $card);

        // Get the form
        $form = $this->createForm(CardType::class, $card, [
            'image_url' => $card->getCardImage(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Reset image if template changed
            $template = $form->get('template')->getData()->getId();
            $oldTemplate = $form->get('old_template')->getData();
            if ($template && (!$oldTemplate || $template !== $oldTemplate)) {
                $card->setCardImage(null);
            }

            $imageFile = $form->get('card_image')->getData();
            $cardService->attachImageToCard($imageFile, $card);

            // Save the card
            $this->entityManager->flush();

            // Redirect to the list page
            return $this->redirectToRoute('app_card_list');
        }

        // Render the form
        return $this->render('card/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/cards/addToCart/{id}', name: 'app_card_add_to_cart')]
    public function addToCart(Card $card): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('VIEW', $card);

        // Get the order repository
        $orderRepository = $this->entityManager->getRepository(Order::class);
        $cart = $orderRepository->findOneBy([
            'user' => $user,
            'status' => 'cart',
        ]);

        if (!$cart) {
            // Create a new cart if it doesn't exist
            $cart = new Order();
            $cart->setUser($user);
            $cart->setStatus('cart');
            $this->entityManager->persist($cart);
        }

        $this->orderService->orderAddCard($cart, $card);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        $this->addFlash('success', 'Card added to cart successfully!');

        // Redirect to the cart page
        return $this->redirectToRoute('app_card_list');
    }

    #[Route('/cards/delete/{id}', name: 'app_card_delete')]
    public function delete(Card $card): Response
    {
        $user = $this->getUser();
        $this->denyAccessUnlessGranted('DELETE', $card);

        // Remove the card
        $this->entityManager->remove($card);
        $this->entityManager->flush();

        // Redirect to the list page
        return $this->redirectToRoute('app_card_list');
    }
}
