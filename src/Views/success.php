<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="ty-page">
  <div class="ty-card">

    <div class="ty-top">
      <div class="ty-icon">
        <svg viewBox="0 0 24 24"><path d="M5 12l4 4L19 7"/></svg>
      </div>
      <h1 class="ty-top-title">Order Confirmed</h1>
      <p class="ty-top-sub">Payment processed successfully</p>
    </div>

    <div class="ty-body">

      <p class="ty-message">
        Thank you, <strong><?= $customer_name ?></strong>. Your order has been confirmed
        and a receipt has been sent to <strong><?= $customer_email ?></strong>.
      </p>

      <p class="ty-section-label">Order Summary</p>
      <table class="ty-table">
        <tr>
          <td>Item</td>
          <td>SwiftCart Product</td>
        </tr>
        <tr>
          <td>Date</td>
          <td><?= $date ?></td>
        </tr>
        <tr>
          <td>Payment</td>
          <td>Visa •••• 4242</td>
        </tr>
      </table>

      <div class="ty-txn">
        <div class="ty-txn-label">Transaction ID</div>
        <div class="ty-txn-id"><?= $transaction_id ?></div>
      </div>

      <div class="ty-total-row">
        <span class="ty-total-label">Total Charged</span>
        <span class="ty-total-amount">$<?= $amount ?> USD</span>
      </div>

      <a href="<?= BASE_URL ?>/" class="ty-btn">Go to home</a>

    </div>

    <div class="ty-footer">Secured by Stripe &nbsp;·&nbsp; Keep this page for your records</div>

  </div>
</div>

</body>
</html>
