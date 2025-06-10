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
            ->setParameter('status', Order::STATUS_CART)
            ->getQuery()
            ->getResult();

        if (empty($cart)) {
            $cart = new Order();
            $cart->setUser($user);
            $cart->setStatus(Order::STATUS_CART);
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

    protected function createOrderNumber(Order $order): string
    {
        // Use year + month + 4 digit ID + 3 random letters
        $date = new \DateTimeImmutable();
        $year = $date->format('y');
        $month = $date->format('m');
        $id = str_pad($order->getId(), 4, '0', STR_PAD_LEFT);
        $id = substr($id, -4); // Keep only the last 4 digits
        $letters = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 3);

        return "$year$month-$letters-$id";
    }

    public function complete(Order $order): Order
    {
        $order->setStatus(Order::STATUS_COMPLETED);
        $order->setPlacedAt(new \DateTimeImmutable());
        $order->setOrderNumber($this->createOrderNumber($order));
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

        return $order;
    }

    public function checkOrder_quantity(Order $order)
    {
        foreach ($order->getOrderItems() as $item) {
            if ($item->getQuantity() <= 0) {
                $order->removeOrderItem($item);
                $this->entityManager->remove($item);
            }
        }
    }

    public function checkOrder_sameItems(Order $order)
    {
        $items = [];
        foreach ($order->getOrderItems() as $item) {
            // TODO implement logic to check for same items
        }
    }
}
