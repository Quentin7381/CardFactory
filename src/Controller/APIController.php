<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\UserEditType;
use App\Entity\User;
use Firebase\JWT\Key;
use Firebase\JWT\JWT;


class APIController extends AbstractController {

    public function __construct(
        private readonly \Doctrine\Persistence\ManagerRegistry $doctrine,
    ) {
    }

    #[Route('/api/sales', name: 'api_sales', methods: ['GET'])]
    public function sales(Request $request){
        // Get JWT token from the request header
        $token = $request->headers->get('Authorization');
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return $this->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // Extract the JWT token user
        $jwt = substr($token, 7);

        // Decode using firebase/php-jwt
        try {
            $headers = (object) [
                'alg' => 'HS256',
                'typ' => 'JWT',
                'kid' => '1', 
            ];
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'), $headers);
        } catch (\Exception $e) {
            throw new \Exception('Invalid token: ' . $e->getMessage());
            return $this->json(['error' => 'Invalid token'], Response::HTTP_UNAUTHORIZED);
        }

        // Check if the token isn't expired
        if ($decoded->exp < time()) {
            return $this->json(['error' => 'Token expired'], Response::HTTP_UNAUTHORIZED);
        }

        // Retrieve the user from the database
        $user = $this->doctrine->getRepository(User::class)->find($decoded->sub);
        if (!$user) {
            return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        // Check if the user has the 'ROLE_ADMIN' role
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        return $this->json([
            'detail' => [
                // Example sales data
                ['id' => 1, 'amount' => 100, 'date' => '2023-10-01'],
                ['id' => 2, 'amount' => 200, 'date' => '2023-10-02'],
            ],
            'total' => 300,
            'count' => 2,
        ]);
    }
}