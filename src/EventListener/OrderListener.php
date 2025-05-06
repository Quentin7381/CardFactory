<?php

namespace App\EventListener;

use App\Service\OrderService;

class OrderListener
{

    public function __construct(private OrderService $orderService){
    }

    public function postLoad($order)
    {
        if (!$order instanceof \App\Entity\Order) {
            return;
        }

        $this->orderService->checkOrder($order);
    }
}