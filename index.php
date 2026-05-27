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
define('BASE_URL',  baseUrl());

$products = [
    [
        'name'  => 'Apple MacBook Pro 14"',
        'qty'   => 1,
        'price' => 1999,
    ],
    [
        'name'  => 'Sony WH-1000XM5 Headphones',
        'qty'   => 1,
        'price' => 349,
    ],
    [
        'name'  => 'Samsung 4K Monitor 27"',
        'qty'   => 1,
        'price' => 499,
    ],
    [
        'name'  => 'Logitech MX Master 3 Mouse',
        'qty'   => 2,
        'price' => 99,
    ],
];
$shippingCost = 20;

$data = [
    'products'      => $products,
    'shipping_cost' => $shippingCost,
    'address'       => [
        'name'      => 'John Watson',
        'email'     => 'john@example.com',
        'address'   => '10 Downing Street',
        'city'      => 'New York',
        'post_code' => 'SW1A2AA',
    ]
];

Routes::get('/', function () {
    return view('app', []);
});


Routes::get('/checkout', function () use($data) {
    return view('checkout', $data);
});

Routes::post('/checkout', function () {
    $userInput   = json_decode(file_get_contents("php://input"), true);
    $validInput  = InputController::inputValidate($userInput);
    if (isset($validInput['errors'])) {
        echo json_encode($validInput['errors']);
        exit;
    }
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

Routes::get('/order-confirmation', function (){
 PaymentController::orderConfirmation($_GET['session_id'] ?? null);
});
    
Routes::post('/cancel-subscription', function(){
 PaymentController::cancelSubscription();
});

$routes = Routes::getInstance();
$routes->dispatch();
