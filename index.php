<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

use App\Routes;
use App\Controller\PaymentController;
use App\Controller\InputController;

require_once 'helper.php';
require_once 'config.php';

Routes::get('/', function () {
    return view('app', []);
});

Routes::get('/checkout', function () {
    return view('checkout', []);
});


Routes::post('/checkout', function () {

    $validInput = InputController::inputValidate($_POST);

    var_dump($validInput);

    // PaymentController::createSession();
});

$routes = Routes::getInstance();
$routes->dispatch();
