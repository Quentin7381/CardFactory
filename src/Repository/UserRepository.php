<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Order;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getCart(User $user): ?array
    {
        // Fetch orders with status 'cart' for the given user
        $cart = $this->createQueryBuilder("u")
            ->select("o")
            ->innerJoin("u.orders", "o")
            ->where("o.status = :status")
            ->setParameter("status", "cart")
            ->getQuery()
            ->getResult();

        // If no cart found, create a new one
        if (empty($cart)) {
            $cart = new Order();
            $cart->setUser($user);
            $cart->setStatus("cart");
            $this->_em->persist($cart);
            $this->_em->flush();
        }

        // Return the first cart found
        return $cart[0];
    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
