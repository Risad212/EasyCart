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

        <form class="checkout-form" method="POST" action="<?= BASE_URL ?>/checkout">

            <h3>Billing Details</h3>

            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="John Doe" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="john@example.com" required>
            </div>

            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" value="Mirpur 10, Dhaka" required>
            </div>

            <h3>Order Summary</h3>

            <div class="order-box">
                <p>Product: <strong>SwiftCart Product</strong></p>
                <p>Total: <strong>$20.00</strong></p>
            </div>

            <button type="submit" class="stripe-btn">
                Checkout 💳
            </button>

        </form>

    </main>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        window.STRIPE_KEY = "<?= $_ENV['PUBLISH_KEY'] ?>";
        window.BASE_URL   = "<?= BASE_URL ?>";
    </script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>

</html>