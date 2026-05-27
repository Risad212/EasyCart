<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SwiftCart</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body>

    <main>

        <h2>Checkout Page</h2>

        <div style="text-align: center; margin-top: 50px">
             <a href="<?= BASE_URL ?>/checkout" class="checkout-btn">
              Checkout
           </a>
        </div>

        <div style="text-align: center; margin-top: 50px">
            <form method="POST" action="<?= BASE_URL ?>/cancel-subscription">
                <button type="submit">Cancel Subscription</button>
            </form>
        </div>

    </main>

</body>

</html>
