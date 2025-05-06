<?php

namespace App\Service;

use Stripe\StripeClient;
use App\Entity\OrderItem;

class StripeService
{
    private $stripe_client;

    public function __construct()
    {
        $api_key = $_ENV['STRIPE_API_KEY'];
        if (!$api_key) {
            throw new \Exception('STRIPE_API_KEY not set in environment variables.');
        }

        $this->stripe_client = new StripeClient($api_key);
    }

    public function checkoutInit(OrderItem ...$products)
    {

        $params = [
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'ui_mode' => 'hosted',
            'success_url' => 'localhost:8000/cart/success',
        ];

        foreach ($products as $product) {
            $params['line_items'][] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $product->getLabel(),
                    ],
                    'unit_amount' => $product->getPrice()
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
        ;

        $checkout = $this->stripe_client->request(
            'POST',
            '/v1/checkout/sessions',
            $params,
            []
        );

        dump($checkout);
        exit;
    }

}