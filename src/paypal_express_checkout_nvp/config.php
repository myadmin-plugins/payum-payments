<?php
//config.php
use Payum\Core\PayumBuilder;
use Payum\Core\Payum;
/** @var Payum $payum */
$payum = (new PayumBuilder())
    ->addDefaultStorages()
    ->addGateway('gatewayName', [
        'factory' => 'paypal_express_checkout',
        'username'  => 'change it',
        'password'  => 'change it',
        'signature' => 'change it',
        'sandbox'   => true
    ])
    ->getPayum()
;
