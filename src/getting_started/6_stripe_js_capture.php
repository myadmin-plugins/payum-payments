<?php

$model = new \Payum\Model\Payment();
$model->setCurrencyCode('USD');
$model->setTotalAmount(1);

$gateway->execute(new \Payum\Core\Request\Capture($model));

// or using raw format

$model = [
   'amount' => 100,
   'currency' => 'USD'
];

$gateway->execute(new \Payum\Core\Request\Capture($model));
