<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Failed</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div class="ty-page">
  <div class="ty-card">

    <div class="ty-top ty-top--error">
      <div class="ty-icon">
        <svg viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
      </div>
      <h1 class="ty-top-title">Payment Failed</h1>
      <p class="ty-top-sub">We were unable to process your payment</p>
    </div>

    <div class="ty-body">

      <p class="ty-message">
        Sorry, <strong><?= $customer_name ?></strong>. Your payment could not be completed.
        No charges have been made to your account.
      </p>

      <div class="ty-txn">
        <div class="ty-txn-label">Reason for Failure</div>
        <div class="ty-txn-id"><?= $reason ?></div>
      </div>

      <p class="ty-section-label">What you can try</p>
      <table class="ty-table">
        <tr>
          <td>Check</td>
          <td>Card number, expiry date and CVV</td>
        </tr>
        <tr>
          <td>Ensure</td>
          <td>Sufficient funds are available</td>
        </tr>
        <tr>
          <td>Try</td>
          <td>A different card or payment method</td>
        </tr>
      </table>

      <a href="<?= BASE_URL ?>/checkout" class="ty-btn">Try Again</a>

      <div class="ty-txn" style="margin-top: 1rem;">
        <div class="ty-txn-label">Need Help? Contact Support</div>
        <div class="ty-txn-id">Email: support@example.com</div>
        <div class="ty-txn-id">Phone: +1 (800) 123-4567</div>
        <div class="ty-txn-id">Live Chat: Mon–Fri, 9am–6pm EST</div>
      </div>

    </div>

    <div class="ty-footer">No charges were made &nbsp;·&nbsp; Keep this page for your records</div>

  </div>
</div>

</body>
</html>
