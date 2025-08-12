<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Card;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Template;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Users
        $user1 = new User();
        $user1->setUsername('user1')
            ->setEmail('user1@example.com')
            ->setPassword($this->passwordHasher->hashPassword($user1, 'password1'))
            ->setShareCards(true);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('user2')
            ->setEmail('user2@example.com')
            ->setPassword($this->passwordHasher->hashPassword($user2, 'password2'))
            ->setShareCards(true);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('user3')
            ->setEmail('user3@example.com')
            ->setPassword($this->passwordHasher->hashPassword($user3, 'password3'))
            ->setShareCards(false);
        $manager->persist($user3);

        // Create Templates
        $defaultTemplate = new Template();
        $defaultTemplate->setCssClass('default')
                        ->setName('Default Template')
                        ->setImageWidth(800)
                        ->setImageHeight(600);
        $manager->persist($defaultTemplate);

        $largeTemplate = new Template();
        $largeTemplate->setCssClass('large')
                      ->setName('Large Template')
                      ->setImageWidth(1200)
                      ->setImageHeight(900);
        $manager->persist($largeTemplate);

        // Create Cards
        foreach ([$user1, $user2, $user3] as $user) {
            for ($i = 1; $i <= 2; $i++) {
                $card = new Card();
                $card->setName("Card {$i} by {$user->getUsername()}")
                    ->setAuthor($user)
                    ->setCardTitle("Title {$i}")
                    ->setCardSubtitle("Subtitle {$i}")
                    ->setCardBody("Body content for card {$i} by {$user->getUsername()}.")
                    ->setTemplate($i % 2 === 0 ? $largeTemplate : $defaultTemplate) // Assign template
                    ->setCardImage($i % 2 === 0 ? '/uploads/images/fixture-dragon.jpg' : '/uploads/images/fixture-ninja.webp'); // Assign image
                $manager->persist($card);
            }
        }

        // Create Orders
        foreach ([$user1, $user2, $user3] as $user) {
            for ($i = 1; $i <= 3; $i++) {
                $order = new Order();
                $order->setUser($user)
                    ->setStatus($i === 3 ? Order::STATUS_COMPLETED : Order::STATUS_CART)
                    ->setPlacedAt(new \DateTimeImmutable())
                    ->setOrderNumber("FXT-{$user->getId()}-{$i}");
                $manager->persist($order);

                // Add Order Items
                foreach ($user->getCards() as $card) {
                    $orderItem = new OrderItem();
                    $orderItem->setLabel($card->getOrderLabel())
                        ->setPrice($card->getPrice())
                        ->setQuantity(1)
                        ->setReferencedEntity($card)
                        ->setRelatedOrder($order);
                    $manager->persist($orderItem);
                }
            }
        }

        $manager->flush();
    }
}
