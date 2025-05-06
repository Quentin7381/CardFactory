<?php

namespace App\Entity\Interface;

interface OrderableInterface 
{
    /**
     * Prints out every information about the information needed in an order.
     * 
     * Eg. If you are ordering a t-shirt, brand, model, size and color should be printed out.
     * 
     * @return void
     */
    public function toOrderArray(): array;

    /**
     * Returns the id of the entity.
     * 
     * @return int
     */
    public function getId(): int|string|null;

    public function getOrderLabel(): string;
}