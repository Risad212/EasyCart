<?php

namespace App\Controller;

use Stripe\Checkout\Session;


class PaymentController
{
    public static function createSession()
    {
        header('Content-Type: application/json');

        try {

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'SwiftCart Product',
                        ],
                        'unit_amount' => 2000,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => BASE_URL . 'success.php',
                'cancel_url'  => BASE_URL . 'cancel.php',
            ]);

            echo json_encode(['id' => $session->id]);
            exit;
        } catch (\Throwable $e) {

            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}
