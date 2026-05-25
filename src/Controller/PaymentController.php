<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Throwable;

class PaymentController
{
    /**
     * Create Stripe Checkout Session
     * 
     * @param array $validInput
     * @return void
     */
    public static function createSession(array $validInput): void
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

            $customerEmail = $metaData['email'] ?? '';

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => 'payment',
                'customer_email'       => $customerEmail,

                'metadata'    => [
                    'name'    => $metaData['name'] ?? '',
                    'email'   => $metaData['email'] ?? '',
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

    /**
     * Handle Payment Success Callback
     * 
     * @return mixed
     */
    public static function success(): mixed
    {
        $sessionId = $_GET['session_id'] ?? null;

        if ( !$sessionId ) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        try {
            $session   = Session::retrieve( $sessionId );

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

            if ($session->payment_status !== 'paid') {
                header('Location: ' . BASE_URL . '/failure?reason=Payment not completed');
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

            $customerName   = $session->customer_details->name ?? 'Guest';
            $customerEmail  = $session->customer_details->email ?? '';
            $amount         = number_format($session->amount_total / 100, 2);
            $transactionId  = $session->payment_intent ?? '';
            $date           = date('M j, Y');

            return view('success', [
                'customer_name'  => $customerName,
                'customer_email' => $customerEmail,
                'amount'         => $amount,
                'transaction_id' => $transactionId,
                'date'           => $date,
            ]);

        } catch (Throwable $e) {
            header('Location: ' . BASE_URL . '/failure?reason=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Handle Payment Failure Callback
     * 
     * @return mixed
     */
    public static function failure(): mixed
    {
        $customerName = 'Guest';
        $reason = $_GET['reason'] ?? 'Your payment could not be completed.';

        return view('failure', [
            'customer_name' => $customerName,
            'reason'        => $reason,
        ]);
    }

    /**
     * Verify Payment Transaction 
     * 
     * @param string|null $sessionId
     * @return void
     */
    public static function verifyTransaction(?string $sessionId): void
    {
        if (!$sessionId) {
            header('Location: ' . BASE_URL . '/failure?reason=No session');
            exit;
        }

        try {
            $session = Session::retrieve($sessionId);

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

            header('Location: ' . BASE_URL . '/order-confirmation?session_id=' . $sessionId);
            exit;

        } catch (Throwable $e) {
            header('Location: ' . BASE_URL . '/failure?reason=' . urlencode($e->getMessage()));
            exit;
        }
    }

    /**
     * Confirmation Payment Order
     * 
     * @param string|null $sessionId
     * @return mixed
     */
    public static function orderConfirmation(?string $sessionId): mixed
    {
        if (!$sessionId) {
            header('Location: ' . BASE_URL . '/home');
            exit;
        }

        try {
            $session             = Session::retrieve($sessionId);

            $customerName        = $session->customer_details->name ?? 'Guest';
            $customerEmail       = $session->customer_details->email ?? '';
            $amount              = number_format($session->amount_total / 100, 2);
            $transactionId       = $session->payment_intent ?? '';
            $date                = date('M j, Y');

           // Destroy session after order is confirmed
            session_destroy();

            return view('success', [
                'customer_name'  => $customerName,
                'customer_email' => $customerEmail,
                'amount'         => $amount,
                'transaction_id' => $transactionId,
                'date'           => $date,
            ]);

        } catch (Throwable $e) {
            header('Location: ' . BASE_URL . '/failure?reason=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
