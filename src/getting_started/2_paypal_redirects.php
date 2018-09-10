<?php

$model = new \Payum\Model\Payment();
$model->setCurrencyCode('USD');
$model->setTotalAmount(1);
$model->setDetails(
	[
	'RETURNURL' => 'http://return.url',
	'CANCELURL' => 'http://cancel.url'
	]
);

$gateway->execute(new \Payum\Core\Request\Capture($model));

// or using raw format

$model = [
   'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
   'PAYMENTREQUEST_0_AMT' => 1,
   'RETURNURL' => 'http://return.url',
   'CANCELURL' => 'http://cancel.url'
];

$gateway->execute(new \Payum\Core\Request\Capture($model));
