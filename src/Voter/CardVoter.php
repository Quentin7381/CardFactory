<?php
namespace App\Voter;

use App\Entity\Card;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CardVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return
            in_array($attribute, ['VIEW', 'EDIT', 'DELETE'], true)
            && (
                $subject instanceof Card
            );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Card) {
            return false;
        }

        // Admin users can always view, edit, or delete users
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        switch ($attribute) {
            case 'EDIT':
            case 'DELETE':
            case 'VIEW':
                // Only the owner can view, edit, or delete their own profile
                if ($subject->getAuthor() !== $user) {
                    return false;
                }
                return true;
        }

        // If none of the above, deny access
        return false;
    }
}