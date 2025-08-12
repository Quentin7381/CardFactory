<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Card>
 */
class CardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Card::class);
    }

    /**
     * Finds all cards of users with share_cards enabled, sorted by creation date.
     *
     * @param string $sortOrder 'ASC' for oldest first, 'DESC' for newest first
     * @return Card[] Returns an array of Card objects
     */
    public function findCardsOfUsersWithShareCardsEnabled(string $sortOrder = 'DESC'): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.author', 'u')
            ->andWhere('u.share_cards = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.id', $sortOrder) // Assuming 'id' represents creation order
            ->getQuery()
            ->getResult();
    }
}
