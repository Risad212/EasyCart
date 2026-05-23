<?php
$products = $data['products'];
$address = $data['address'];
$shippingCost = $data['shipping_cost'];

$subtotal = 0;

foreach ($products as $product) {
    $subtotal += $product['price'] * $product['qty'];
}

$total = $subtotal + $shippingCost;

// store data in session
session_start();

$_SESSION['checkout'] = [
    'products' => $products,
    'shipping_cost' => $shippingCost,
    'address' => $address,
    'subtotal' => $subtotal,
    'total' => $total
];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout - SwiftCart</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body>

<header>
    <h2>Checkout</h2>
</header>

<main class="checkout-container">

<div class="checkout-grid">

    <!-- LEFT: FORM -->
    <form class="checkout-form">

        <h3>Billing Details</h3>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= $address['name'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $address['email'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?= $address['address'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="<?= $address['city'] ?? '' ?>" required>
        </div>

        <div class="form-group">
            <label>Postal Code</label>
            <input type="text" name="postal_code" value="<?= $address['post_code'] ?? '' ?>" required>
        </div>

        <button type="submit" class="stripe-btn">
            Checkout 💳
        </button>

    </form>

    <!-- RIGHT: ORDER SUMMARY -->
    <div class="order-box">

        <h3>Order Summary</h3>

        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <div class="info">
                        <p><strong><?= $product['name'] ?? '' ?></strong></p>
                        <p>$<?= $product['price'] ?? 0 ?> × <?= $product['qty'] ?? 0 ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No products in cart</p>
        <?php endif; ?>

        <p>Subtotal: <strong>$<?= number_format($subtotal, 2) ?></strong></p>
        <p>Shipping: <strong>$<?= number_format($shippingCost, 2) ?></strong></p>
        <p>Total: <strong>$<?= number_format($total, 2) ?></strong></p>

    </div>

</div>

</main>

<script src="https://js.stripe.com/v3/"></script>

<script>
    window.STRIPE_KEY = "<?= $_ENV['PUBLISH_KEY'] ?>";
    window.BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>

</body>
</html>
