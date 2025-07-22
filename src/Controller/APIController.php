<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\SalesStatsService;
use App\Service\JWTService;


class APIController extends AbstractController {

    public function __construct(
        private readonly \Doctrine\Persistence\ManagerRegistry $doctrine,
    ) {
    }

    #[Route('/api/sales', name: 'api_sales', methods: ['GET'])]
    public function sales(Request $request, SalesStatsService $salesStatsService, JWTService $jwtService): Response{
        // Get JWT token fromsub the request header
        $token = $request->headers->get('Authorization');
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Extract the JWT token user
        $jwt = substr($token, 7);

        // Check JWT
        if (!$jwtService->checkJWT($jwt)) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Check user
        $user = $jwtService->getUserByJWT($jwt);

        if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Forbidden'], Response::HTTP_FORBIDDEN);
        }

        // Get dates
        $dateStart = $request->query->get('start', null);
        $dateEnd = $request->query->get('end', null);

        // Use SalesStatsService to get sales data
        return $this->json(
            $salesStatsService->getSalesStats(
                $dateStart ? new \DateTimeImmutable($dateStart) : null,
                $dateEnd ? new \DateTimeImmutable($dateEnd) : null
            ),
            Response::HTTP_OK,
            [],
            ['groups' => 'sales_stats']
        );
    }
}