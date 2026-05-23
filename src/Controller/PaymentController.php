<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Throwable;

class PaymentController
{
  public static function createSession($validInput)
   {
        session_start();
        header('Content-Type: application/json');
        try {

            $checkout     = $_SESSION['checkout'] ?? [];
            $products     = $checkout['products'] ?? [];
            $shippingCost = $checkout['shipping_cost'] ?? 0;
            $metaData     = $validInput['data'] ?? [];
            $lineItems    = [];

            foreach ($products as $product) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $product['name'],
                        ],
                        'unit_amount' => $product['price'] * 100,
                    ],
                    'quantity' => $product['qty'],
                ];
            }

            if ($shippingCost > 0) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'Shipping Cost',
                        ],
                        'unit_amount' => $shippingCost * 100,
                    ],
                    'quantity' => 1,
                ];
            }

            if (empty($lineItems)) {
                echo json_encode(['error' => 'Cart is empty']);
                exit;
            }

            $customer_email = $metaData['email'] ?? '';

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'customer_email' => $customer_email,
                'metadata' => [
                    'name' => $metaData['name'] ?? '',
                    'email' => $metaData['email'] ?? '',
                    'address' => trim(
                        ($metaData['address'] ?? '') . ', ' .
                        ($metaData['city'] ?? '') . ' ' .
                        ($metaData['postal_code'] ?? '')
                    ),
                ],

                'success_url' => BASE_URL . '/verify-payment?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => BASE_URL . '/failure',
            ]);

            // ✅ RETURN SESSION ID
            echo json_encode(['id' => $session->id]);
            exit;

        } catch (\Throwable $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    public static function success()
    {
        $session_id = $_GET['session_id'] ?? null;

        if (!$session_id) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        try {
            $session = Session::retrieve($session_id);

            // ✅ CHECK 1 — verify amount matches cart total
            $checkout  = $_SESSION['checkout'] ?? [];
            $products  = $checkout['products'] ?? [];
            $shipping  = $checkout['shipping_cost'] ?? 0;

            $cartTotal = 0;
            foreach ($products as $product) {
                $cartTotal += $product['price'] * $product['qty'];
            }
            $cartTotal += $shipping;
            $cartTotal = round($cartTotal * 100);

            if ($session->amount_total !== $cartTotal) {
                header('Location: ' . BASE_URL . '/failure?reason=Amount mismatch');
                exit;
            }

            // ✅ CHECK 2 — payment actually paid
            if ($session->payment_status !== 'paid') {
                header('Location: ' . BASE_URL . '/failure?reason=Payment not completed');
                exit;
            }

            // ✅ CHECK 3 — transaction ID exists
            if (empty($session->payment_intent)) {
                header('Location: ' . BASE_URL . '/failure?reason=Invalid transaction');
                exit;
            }

            // ✅ CHECK 4 — amount is valid
            if ($session->amount_total <= 0) {
                header('Location: ' . BASE_URL . '/failure?reason=Invalid amount');
                exit;
            }

            $customer_name  = $session->customer_details->name ?? 'Guest';
            $customer_email = $session->customer_details->email ?? '';
            $amount         = number_format($session->amount_total / 100, 2);
            $transaction_id = $session->payment_intent ?? '';
            $date           = date('M j, Y');

            return view('success', [
                'customer_name'  => $customer_name,
                'customer_email' => $customer_email,
                'amount'         => $amount,
                'transaction_id' => $transaction_id,
                'date'           => $date,
            ]);

        } catch (Throwable $e) {
            header('Location: ' . BASE_URL . '/failure?reason=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public static function failure()
    {
        $customer_name = 'Guest';
        $reason = $_GET['reason'] ?? 'Your payment could not be completed.';

        return view('failure', [
            'customer_name' => $customer_name,
            'reason' => $reason,
        ]);
    }

    public static function verifyTransaction($session_id)
    {
        // ✅ handle null session_id
        if (!$session_id) {
            header('Location: ' . BASE_URL . '/failure?reason=No session');
            exit;
        }

        try {
            $session = Session::retrieve($session_id);

            if ($session->payment_status !== 'paid') {
                header('Location: ' . BASE_URL . '/failure?reason=Payment not verified');
                exit;
            }

            if (empty($session->payment_intent)) {
                header('Location: ' . BASE_URL . '/failure?reason=Invalid transaction');
                exit;
            }

            if ($session->amount_total <= 0) {
                header('Location: ' . BASE_URL . '/failure?reason=Invalid amount');
                exit;
            }

            header('Location: ' . BASE_URL . '/order-confirmation?session_id=' . $session_id);
            exit;

        } catch (Throwable $e) {
            header('Location: ' . BASE_URL . '/failure?reason=' . urlencode($e->getMessage()));
            exit;
        }
    }
    public static function orderConfirmation($session_id)
    {
        if (!$session_id) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        try {
            $session = Session::retrieve($session_id);

            $customer_name  = $session->customer_details->name ?? 'Guest';
            $customer_email = $session->customer_details->email ?? '';
            $amount         = number_format($session->amount_total / 100, 2);
            $transaction_id = $session->payment_intent ?? '';
            $date           = date('M j, Y');

            return view('success', [
                'customer_name'  => $customer_name,
                'customer_email' => $customer_email,
                'amount'         => $amount,
                'transaction_id' => $transaction_id,
                'date'           => $date,
            ]);

        } catch (Throwable $e) {
            header('Location: ' . BASE_URL . '/failure?reason=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
