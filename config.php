<?php

use Dotenv\Dotenv;
use Stripe\Stripe;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

Stripe::setApiKey($_ENV['SECRET_KEY']);
