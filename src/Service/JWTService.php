<?php

namespace App\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Entity\JWT as JWTEntity;
use App\Entity\User;
use App\Repository\JWTRepository;
use App\Repository\UserRepository;
class JWTService
{
    public function __construct(
        private readonly JWTRepository $jwtRepository,
        private readonly UserRepository $userRepository
    ) {
    }

    public function generateJWT(string $userId)
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new \Exception('User not found.');
        }

        $payload = [
            'uid' => $userId,
            'exp' => time() + 3600, // Token valid for 1 hour
            'iat' => time(),
            'jti' => bin2hex(random_bytes(16))
        ];

        $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        // Save the JWT to the database
        $jwtEntity = new JWTEntity();
        $jwtEntity->setFullstring($jwt);
        $jwtEntity->setJti($payload['jti']);
        $jwtEntity->setExp($payload['exp']);
        $jwtEntity->setUser($user);
        
        $entityManager = $this->jwtRepository->getEntityManager();
        $entityManager->persist($jwtEntity);
        $entityManager->flush();

        return $jwt;
    }

    public function checkJWT(string $jwt): bool
    {
        // Decode the JWT
        try {
            $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
            $jti = $decoded->jti;
        } catch (\Exception $e) {
            return false; // Invalid JWT
        }

        // Check if JTI exists in the database
        $jwtEntity = $this->jwtRepository->findOneBy(['jti' => $jti]);
        if (!$jwtEntity) {
            return false; // JTI not found or expired
        }

        // Check if the JWT is expired
        if ($jwtEntity->getExp() < time()) {
            return false; // JWT is expired
        }

        // Check if the JWT string is the same
        if ($jwtEntity->getFullstring() !== $jwt) {
            return false; // JWT string mismatch
        }

        return true; // JWT is valid
    }

    public function invalidateJWT(string $jti): void
    {
        // Find the JWT entity by JTI
        $jwtEntity = $this->jwtRepository->findOneBy(['jti' => $jti]);
        if ($jwtEntity) {
            // Remove the JWT from the database
            $this->jwtRepository->remove($jwtEntity, true);
        }
    }

    public function getUserByJWT(string $jwt): ?User
    {
        if (!$this->checkJWT($jwt)) {
            return null; // Invalid JWT
        }

        // Decode the JWT to get the user ID
        $decoded = JWT::decode($jwt, new Key($_ENV['JWT_SECRET'], 'HS256'));
        $userId = $decoded->uid;

        // Find the user by ID
        return $this->userRepository->find($userId);
    }
}