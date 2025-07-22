<?php

namespace App\Service;

use App\Entity\Order;

class SalesStatsService
{
    public function __construct(
        private readonly \Doctrine\Persistence\ManagerRegistry $doctrine,
    ) {
    }

    public function getSalesStats($dateStart = null, $dateEnd = null): array
    {
        // Defaults values
        if(!$dateStart) {
            $dateStart = new \DateTimeImmutable('1970-01-01');
        }

        if(!$dateEnd) {
            $dateEnd = new \DateTimeImmutable('now');
        }

        // Make sure dateStart is before dateEnd
        if ($dateStart > $dateEnd) {
            [$dateStart, $dateEnd] = [$dateEnd, $dateStart];
        }

        $entityManager = $this->doctrine->getManager();
        $queryBuilder = $entityManager->createQueryBuilder();

        $queryBuilder
            ->select('COUNT(o.id) as saleCount, SUM(oi.price * oi.quantity) as totalSales') // Aggregate functions
            ->from(Order::class, 'o')
            ->join('o.orderItems', 'oi') // Join with OrderItem table
            ->where('o.placedAt BETWEEN :dateStart AND :dateEnd')
            ->andWhere('o.status = :status')
            ->setParameter('dateStart', $dateStart)
            ->setParameter('dateEnd', $dateEnd)
            ->setParameter('status', 'completed');

        $sales = $queryBuilder->getQuery()->getSingleResult();

        return [
            'count' => $sales['saleCount'] ?? 0,
            'total' => $sales['totalSales'] ?? 0,
        ];
    }
}