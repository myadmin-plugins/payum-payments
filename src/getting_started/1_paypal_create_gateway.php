<?php

require_once 'vendor/autoload.php';
include_once __DIR__ . '/include/functions.inc.php';

use Payum\Paypal\ExpressCheckout\PaypalExpressCheckoutGatewayFactory;

$factory = new PaypalExpressCheckoutGatewayFactory();

$gateway = $factory->create(
    [
    'username' => PAYPAL_API_USERNAME,
    'password' => PAYPAL_API_PASSWORD,
    'signature' => PAYPAL_API_SIGNATURE,
    'sandbox' => false
    ]
);
