<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\UserEditType;

class UserController extends AbstractController {

    public function __construct(
        private readonly \Doctrine\Persistence\ManagerRegistry $doctrine,
    ) {
    }

    #[Route('/user/account', name: 'app_user_account')]
    public function userAccount(): RedirectResponse|Response {
        // Check if the user is logged in
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('error', 'You must be logged in to access this page.');
            return $this->redirectToRoute('app_login');
        }

        // Get the logged-in user
        $user = $this->getUser();
        // if (!$user instanceof User) {
        //     throw new \LogicException('User is not an instance of User entity.');
        // }

        // Render the user account page
        return $this->render('user/account.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/account/edit', name: 'app_user_account_edit')]
    public function userAccountEdit(Request $request): RedirectResponse|Response {
        // Check if the user is logged in
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('error', 'You must be logged in to access this page.');
            return $this->redirectToRoute('app_login');
        }

        // Get the logged-in user
        $user = $this->getUser();
        // if (!$user instanceof User) {
        //     throw new \LogicException('User is not an instance of User entity.');
        // }

        // Init form
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        // Handle form submission
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the data from the form
            $data = $form->getData();

            // Update the user entity
            $user->setUsername($data->getUsername());
            $user->setEmail($data->getEmail());
            $user->setAddress($data->getAddress());

            // Save the changes to the database
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Add a success message
            $this->addFlash('success', 'Your account has been updated successfully.');

            // Redirect to the account page
            return $this->redirectToRoute('app_user_account');
        }

        // Render the user account edit page
        return $this->render('user/account_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}