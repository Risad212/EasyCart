<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require_once 'helper.php';
require_once 'config.php';

use App\Routes;
use App\Controller\PaymentController;
use App\Controller\InputController;

define('BASE_PATH', __DIR__);
define('BASE_URL', baseUrl());

$products = [
    [
        'name'  => 'Minimalist Leather Backpack',
        'qty'   => 1,
        'price' => 120,
    ],
    [
        'name'  => 'Wireless Noise-Canceling Headphones',
        'qty'   => 1,
        'price' => 250,
    ],
    [
        'name'  => 'Smart Fitness Watch',
        'qty'   => 1,
        'price' => 199,
    ],
    [
        'name'  => 'Portable Bluetooth Speaker',
        'qty'   => 1,
        'price' => 89,
    ],
];
$shippingCost = 10;

$data = [
    'products' => $products,
    'shipping_cost' => $shippingCost,
    'address' => [
        'name' => 'Sherlock Holmes',
        'email' => 'sherlock@example.com',
        'address' => '221B Baker Street, London, England',
        'city' => 'London',
        'post_code' => 'NW16XE',
    ]
];


Routes::get('/', function () {
    return view('app', []);
});


Routes::get('/checkout', function () use($data) {
    return view('checkout', $data);
});

Routes::post('/checkout', function () {
    $data       = json_decode(file_get_contents("php://input"), true);
    $validInput = InputController::inputValidate($data);
    PaymentController::createSession($validInput);
});

Routes::get('/success', function () {
    return PaymentController::success();
});

Routes::get('/failure', function () {
    return PaymentController::failure();
});

Routes::get('/verify-payment', function () {
     PaymentController::verifyTransaction($_GET['session_id'] ?? null);
});

Routes::get('/order-confirmation', fn() =>
    PaymentController::orderConfirmation($_GET['session_id'] ?? null)
);


$routes = Routes::getInstance();
$routes->dispatch();
