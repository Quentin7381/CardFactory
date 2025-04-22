<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\OrderItem;
use App\Entity\Card;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getCartByUser(User $user){
        $cart = $this->createQueryBuilder('o')
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
            $this->getEntityManager()->persist($cart);
            $this->getEntityManager()->flush();
            return $cart;
        }

        return $cart[0];
    }

    public function orderAddItem(Order $order, string $label, int $price, ?object $entity = null): Order
    {
        $orderItem = new OrderItem();
        $orderItem->setLabel($label);
        $orderItem->setPrice($price);
        if ($entity) {
            $orderItem->setReferencedEntity($entity);
        }
        $orderItem->setRelatedOrder($order);
        $this->getEntityManager()->persist($orderItem);
        $this->getEntityManager()->flush();

        return $order;
    }

    public function orderAddCard(Order $order, Card $card): Order
    {
        $price = $card->getPrice();
        $label = $card->getName();

        $this->orderAddItem($order, $label, $price, $card);

        return $order;
    }

//    /**
//     * @return Order[] Returns an array of Order objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Order
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
