<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Card;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class OrderService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getCartByUser(User $user): Order
    {
        $cart = $this->entityManager->getRepository(Order::class)
            ->createQueryBuilder('o')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->andWhere('o.status = :status')
            ->setParameter('status', 'cart')
            ->getQuery()
            ->getResult();

        if (empty($cart)) {
            $cart = new Order();
            $cart->setUser($user);
            $cart->setStatus("cart");
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
            return $cart;
        }

        return $cart[0];
    }

    public function orderAddItem(Order $order, string $label, int $price, ?object $entity = null, $quantity = 1): Order
    {
        $orderItem = new OrderItem();
        $orderItem->setLabel($label);
        $orderItem->setPrice($price);
        if ($entity) {
            $orderItem->setReferencedEntity($entity);
        }
        $orderItem->setRelatedOrder($order);
        $orderItem->setQuantity($quantity);
        $this->entityManager->persist($orderItem);
        $this->entityManager->flush();

        return $order;
    }

    public function orderAddCard(Order $order, Card $card): Order
    {
        $price = $card->getPrice();
        $label = $card->getName();

        return $this->orderAddItem($order, $label, $price, $card);
    }

    public function orderRemoveItem(Order $order, OrderItem $orderItem): Order
    {
        $order->removeOrderItem($orderItem);
        $this->entityManager->remove($orderItem);
        $this->entityManager->flush();

        return $order;
    }

    public function orderReset(Order $order): Order
    {
        foreach ($order->getOrderItems() as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();

        return $order;
    }

    public function complete(Order $order): Order
    {
        $order->setStatus('completed');
        $order->setPlacedAt(new \DateTimeImmutable());
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return $order;
    }

    # ------ ORDER INTEGRITY ------ #

    public function checkOrder(Order $order)
    {
        $classMethods = get_class_methods($this);
        foreach ($classMethods as $method) {
            if (str_starts_with($method, 'checkOrder_')) {
                $this->$method($order);
            }
        }
    }

    public function checkOrder_quantity(Order $order)
    {
        foreach ($order->getOrderItems() as $item) {
            if ($item->getQuantity() <= 0) {
                $this->entityManager->remove($item);
            }
        }
    }

    public function checkOrder_sameItems(Order $order)
    {
        $items = [];
        foreach ($order->getOrderItems() as $item) {
            dump($item) ; exit;
        }
    }
}
