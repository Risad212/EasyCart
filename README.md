```markdown
# EasyCart - PHP Checkout System

A simple and elegant checkout system built with vanilla PHP and Stripe payment gateway integration.

## Features

- 🛒 Product checkout system
- 💳 Stripe payment integration
- 🔄 Recurring subscription billing
- ✅ Payment verification
- 📦 Order confirmation
- ❌ Subscription cancellation

## Requirements

- PHP 8.1 or higher
- Composer
- Stripe account

## Installation

### 1. Clone the repository
```bash
git clone git@github.com:yourusername/easycart.git
cd easycart
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure environment
```bash
cp .env.example .env
```

Edit `.env` file:
```env
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxx
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxx
STRIPE_PRICE_ID=price_xxxxxxxxxx
```

### 4. Run the project
```bash
php -S localhost:8000
```

Visit `http://localhost:8000`

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | Home page |
| GET | `/checkout` | Checkout page |
| POST | `/checkout` | Process payment |
| GET | `/verify-payment` | Verify payment |
| GET | `/order-confirmation` | Order confirmation |
| GET | `/success` | Payment success |
| GET | `/failure` | Payment failure |
| POST | `/cancel-subscription` | Cancel subscription |

## Payment Types

| Type | Description |
|------|-------------|
| One Time | Single payment |
| Recurring | Monthly subscription |

## Test Cards

| Card | Number | Use |
|------|--------|-----|
| Success | `4242 4242 4242 4242` | Payment succeeds |
| Declined | `4000 0000 0000 0002` | Payment declined |
| Insufficient funds | `4000 0000 0000 9995` | Insufficient funds |

Use any future expiry date, any 3-digit CVC, any 5-digit ZIP.

## Project Structure

```
easycart/
├── src/
│   ├── Views/
│   │   ├── app.php
│   │   ├── checkout.php
│   │   ├── success.php
│   │   ├── failure.php
│   │   └── subscription-cancelled.php
│   └── Controller/
│       ├── PaymentController.php
│       └── InputController.php
├── assets/
│   ├── css/
│   └── js/
├── vendor/
├── .env
├── .env.example
├── composer.json
├── config.php
├── helper.php
└── index.php
```

## License
MIT
```

Copy and save as `README.md` in your project root. ✅
