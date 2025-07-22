<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\RegistrationFormType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Service\JWTService;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,

    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if the user is already logged in, redirect to the home page
        if ($this->getUser()) {
            $this->addFlash('info', 'You are already logged in.');
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            $this->addFlash('info', 'You are already logged in.');
            return $this->redirectToRoute('home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Check if the password and confirmation match
            if ($form->get('plainPassword')->getData() !== $form->get('passwordConfirm')->getData()) {
                $this->addFlash('error', 'Passwords do not match.');
                return $this->redirectToRoute('register');
            }

            // Check if terms are accepted
            if (!$form->get('terms')->getData()) {
                $this->addFlash('error', 'You must accept the terms and conditions.');
                return $this->redirectToRoute('register');
            }

            // Hash the plain password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Save the user
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirect or log in the user
            return $this->redirectToRoute('home');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/login', name: 'api_login')]
    public function apiLogin(Request $request, JWTService $jtiService): Response
    {

        // Decode JSON request
        $json = $request->getContent();
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->json(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        // Extract email and password
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!is_string($email) || !is_string($password)) {
            return $this->json(['error' => 'Email and password must be provided as strings'], Response::HTTP_BAD_REQUEST);
        }

        // Check if username and password are provided
        if (!$email || !$password) {
            return $this->json(['error' => "email and password are required"], Response::HTTP_BAD_REQUEST);
        }

        // Load user
        $user = $this->userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['error' => 'Invalid username or password'], Response::HTTP_UNAUTHORIZED);
        }

        // Check password
        if (!password_verify($password, $user->getPassword())) {
            return $this->json(['error' => 'Invalid username or password'], Response::HTTP_UNAUTHORIZED);
        }

        // Check role
        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json(['error' => 'You do not have permission to access this resource'], Response::HTTP_FORBIDDEN);
        }

        // Generate JWT token using firebase/php-jwt
        $jwt = $jtiService->generateJWT($user->getId());

        // Return
        return $this->json([
            'message' => 'Login successful',
            'user' => $user->getId(),
            'token' => $jwt,
        ]);
    }
}
