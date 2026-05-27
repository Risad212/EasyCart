<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Subscription;
use Throwable;

class PaymentController
{
    /**
     * Create Stripe Checkout Session.
     *
     * @param array $validInput Validated customer input data
     * @return void
     */
    public static function createSession(array $validInput): void
    {
        session_start();
        header('Content-Type: application/json');

        try {
            $checkout      = $_SESSION['checkout'] ?? [];
            $products      = $checkout['products'] ?? [];
            $shippingCost  = $checkout['shipping_cost'] ?? 0;
            $metaData      = $validInput['data'] ?? [];
            $paymentType   = $metaData['payment_type'] ?? 'one-time';
            $mode          = $paymentType === 'recurring' ? 'subscription' : 'payment';
            $stripePriceId = $_ENV['STRIPE_PRICE_ID'] ?? '';
            $lineItems     = [];

            if ($mode === 'subscription') {

                // Subscription — use pre-created price ID from Stripe dashboard
                $lineItems = [
                    [
                        'price'    => $stripePriceId,
                        'quantity' => 1,
                    ]
                ];

            } else {

                // One-time — use price_data for each product
                foreach ($products as $product) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'usd',
                            'product_data' => [
                                'name' => $product['name'],
                            ],
                            'unit_amount'  => $product['price'] * 100,
                        ],
                        'quantity' => $product['qty'],
                    ];
                }

                // Add shipping cost for one-time payment only
                if ($shippingCost > 0) {
                    $lineItems[] = [
                        'price_data' => [
                            'currency'     => 'usd',
                            'product_data' => [
                                'name' => 'Shipping Cost',
                            ],
                            'unit_amount'  => $shippingCost * 100,
                        ],
                        'quantity' => 1,
                    ];
                }
            }

            if (empty($lineItems)) {
                echo json_encode(['error' => 'Cart is empty']);
                exit;
            }

            $customerEmail = $metaData['email'] ?? '';

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => $lineItems,
                'mode'                 => $mode,
                'customer_email'       => $customerEmail,
                'metadata'             => [
                    'name'         => $metaData['name'] ?? '',
                    'email'        => $metaData['email'] ?? '',
                    'payment_type' => $paymentType,
                    'address'      => trim(
                        ($metaData['address'] ?? '') . ', ' .
                        ($metaData['city'] ?? '') . ' ' .
                        ($metaData['postal_code'] ?? '')
                    ),
                ],
                'success_url' => BASE_URL . '/verify-payment?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'  => BASE_URL . '/failure',
            ]);

            echo json_encode(['id' => $session->id]);
            exit;

        } catch (\Throwable $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Handle Payment Success Callback.
     *
     * @return mixed
     */
    public static function success(): mixed
    {
        $sessionId = $_GET['session_id'] ?? null;

        if (!$sessionId) {
            redirect('home');
        }

        try {
            $session     = Session::retrieve($sessionId);
            $checkout    = $_SESSION['checkout'] ?? [];
            $products    = $checkout['products'] ?? [];
            $shipping    = $checkout['shipping_cost'] ?? 0;
            $paymentType = $session->metadata->payment_type ?? 'one-time';

            $cartTotal = 0;
            foreach ($products as $product) {
                $cartTotal += $product['price'] * $product['qty'];
            }
            $cartTotal += $shipping;
            $cartTotal = round($cartTotal * 100);

            // Skip amount check for subscription
            if ($paymentType !== 'recurring') {
                if ($session->amount_total !== $cartTotal) {
                    redirect('failure?reason=Amount mismatch');
                }
            }

            if ($session->payment_status !== 'paid') {
                redirect('failure?reason=Payment not completed');
            }

            // Subscription uses subscription ID, one-time uses payment_intent
            $transactionId = $session->payment_intent ?? $session->subscription ?? '';
            if (empty($transactionId)) {
                redirect('failure?reason=Invalid transaction');
            }

            // Skip amount check for subscription
            if ($paymentType !== 'recurring' && $session->amount_total <= 0) {
                redirect('failure?reason=Invalid amount');
            }

            $customerName  = $session->customer_details->name ?? 'Guest';
            $customerEmail = $session->customer_details->email ?? '';
            $amount        = number_format(($session->amount_total ?? 0) / 100, 2);
            $date          = date('M j, Y');

            return view('success', [
                'customer_name'  => $customerName,
                'customer_email' => $customerEmail,
                'amount'         => $amount,
                'transaction_id' => $transactionId,
                'date'           => $date,
            ]);

        } catch (Throwable $e) {
            redirect('failure?reason=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Handle Payment Failure or Cancellation.
     *
     * @return mixed
     */
    public static function failure(): mixed
    {
        $customerName = 'Guest';
        $reason       = $_GET['reason'] ?? 'Your payment could not be completed.';

        return view('failure', [
            'customer_name' => $customerName,
            'reason'        => $reason,
        ]);
    }

    /**
     * Verify Stripe Payment Transaction.
     *
     * @param string|null $sessionId
     * @return void
     */
    public static function verifyTransaction(?string $sessionId): void
    {
        if (!$sessionId) {
            redirect('failure?reason=No session');
        }

        try {
            $session     = Session::retrieve($sessionId);
            $paymentType = $session->metadata->payment_type ?? 'one-time';

            // Subscription uses session status, one-time uses payment_status
            if ($paymentType === 'recurring') {
                if ($session->status !== 'complete') {
                    redirect('failure?reason=Payment not verified');
                }
            } else {
                if ($session->payment_status !== 'paid') {
                    redirect('failure?reason=Payment not verified');
                }
            }

            // Check transaction ID for both payment types
            $transactionId = $session->payment_intent ?? $session->subscription ?? '';
            if (empty($transactionId)) {
                redirect('failure?reason=Invalid transaction');
            }

            // Skip amount check for subscription
            if ($paymentType !== 'recurring' && $session->amount_total <= 0) {
                redirect('failure?reason=Invalid amount');
            }

            redirect('order-confirmation?session_id=' . $sessionId);

        } catch (Throwable $e) {
            redirect('failure?reason=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Display Order Confirmation Page.
     *
     * @param string|null $sessionId
     * @return mixed
     */
    public static function orderConfirmation(?string $sessionId): mixed
    {
        if (!$sessionId) {
            redirect('home');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            $session       = Session::retrieve($sessionId);
            $customerName  = $session->customer_details->name ?? 'Guest';
            $customerEmail = $session->customer_details->email ?? '';
            $date          = date('M j, Y');
            $paymentType   = $session->metadata->payment_type ?? 'one-time';
            $transactionId = $session->payment_intent ?? $session->subscription ?? '';
            $amount        = number_format(($session->amount_total ?? 0) / 100, 2);

            // Get next billing date for subscription
            $nextBillingDate = '';
            if ($paymentType === 'recurring' && $session->subscription) {
                $subscription    = Subscription::retrieve($session->subscription);
                $nextBillingDate = date('M j, Y', $subscription->current_period_end);
            }

            // Save subscription ID to session for cancellation
            if ($paymentType === 'recurring') {
                $_SESSION['subscription_id'] = $transactionId;
            }

            return view('success', [
                'customer_name'     => $customerName,
                'customer_email'    => $customerEmail,
                'amount'            => $amount,
                'transaction_id'    => $transactionId,
                'date'              => $date,
                'payment_type'      => $paymentType,
                'next_billing_date' => $nextBillingDate,
            ]);

        } catch (Throwable $e) {
            redirect('failure?reason=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Cancel Stripe Subscription.
     *
     * @return mixed
     */
    public static function cancelSubscription(): mixed
    {
        session_start();

        $subscriptionId = $_SESSION['subscription_id'] ?? null;

        if (!$subscriptionId) {
            redirect('failure?reason=No subscription ID');
        }

        try {
            $subscription = Subscription::retrieve($subscriptionId);
            $subscription->cancel();

            // Remove subscription ID from session
            unset($_SESSION['subscription_id']);

            // Destroy session after cancellation
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }

            return view('subscription-cancelled', [
                'status' => $subscription->status,
            ]);

        } catch (Throwable $e) {
            redirect('failure?reason=' . urlencode($e->getMessage()));
        }
    }
}
