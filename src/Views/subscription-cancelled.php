<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Cancelled</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="ty-page">
    <div class="ty-card">

        <?php if ($status === 'canceled'): ?>

            <div class="ty-top">
                <div class="ty-icon ty-icon--cancel">
                    <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
                </div>
                <h1 class="ty-top-title">Subscription Cancelled</h1>
                <p class="ty-top-sub">Your subscription has been successfully cancelled</p>
            </div>

            <div class="ty-body">
                <p class="ty-message">
                    Your subscription has been cancelled. You will not be charged again.
                    You can resubscribe anytime from the checkout page.
                </p>

                <table class="ty-table">
                    <tr>
                        <td>Status</td>
                        <td><span class="ty-status-cancelled">Cancelled</span></td>
                    </tr>
                    <tr>
                        <td>Date</td>
                        <td><?= date('M j, Y') ?></td>
                    </tr>
                </table>

            </div>

        <?php else: ?>

            <div class="ty-top">
                <div class="ty-icon ty-icon--error">
                    <svg viewBox="0 0 24 24"><path d="M12 9v4m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
                </div>
                <h1 class="ty-top-title">Something Went Wrong</h1>
                <p class="ty-top-sub">Status: <?= $status ?></p>
            </div>

            <div class="ty-body">
                <p class="ty-message">
                    We could not cancel your subscription. Please contact support.
                </p>
            </div>

        <?php endif; ?>

        <div class="ty-body">
            <a href="<?= BASE_URL ?>/" class="ty-btn">Go to Home</a>
        </div>

        <div class="ty-footer">Secured by Stripe &nbsp;·&nbsp; Need help? Contact support@example.com</div>

    </div>
</div>

</body>
</html>
