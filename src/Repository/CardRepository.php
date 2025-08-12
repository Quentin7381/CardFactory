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
     * Finds all cards of users with share_cards enabled, optionally filtered by template class, sorted by creation date.
     *
     * @param string $sortOrder 'ASC' for oldest first, 'DESC' for newest first
     * @param string|null $templateClass Optional CSS template class to filter by
     * @return Card[] Returns an array of Card objects
     */
    public function findSharedCards(string $sortOrder = 'DESC', ?string $templateClass = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.author', 'u')
            ->andWhere('u.share_cards = :enabled')
            ->setParameter('enabled', true)
            ->orderBy('c.id', $sortOrder); // Assuming 'id' represents creation order

        if ($templateClass) {
            $qb->innerJoin('c.template', 't')
                ->andWhere('t.css_class = :templateClass')
                ->setParameter('templateClass', $templateClass);
        }

        return $qb->getQuery()->getResult();
    }
}
