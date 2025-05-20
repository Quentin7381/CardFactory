<?php
namespace App\Security;

use App\Entity\Order;
use App\Entity\OrderItem;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        return
            in_array($attribute, ['VIEW', 'EDIT', 'DELETE'], true)
            && (
                $subject instanceof Order
                || $subject instanceof OrderItem
            );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Order && !$subject instanceof OrderItem) {
            return false;
        }

        // Admin users can always edit or delete orders
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return true;
        }

        $order = $subject instanceof OrderItem ? $subject->getOrder() : $subject;

        switch ($attribute) {
            case 'EDIT':
            case 'DELETE':
                // Orders should not be editable or deletable if they are completed
                if($order->getStatus() === Order::STATUS_COMPLETED) {
                    return false;
                }
            case 'VIEW':
                // Only the user who created the order can view, edit, or delete it
                if(!$order->getUser() == $user) {
                    return false;
                }
                return true;
        }

        // If none of the above, deny access
        return false;
    }
}