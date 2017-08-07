<?php
//prepare.php

include 'config.php';

use Payum\Paypal\ExpressCheckout\Nvp\Api;

$storage = $payum->getStorage($agreementClass);

$agreement = $storage->create();
$agreement['PAYMENTREQUEST_0_AMT'] = 0;
$agreement['L_BILLINGTYPE0'] = Api::BILLINGTYPE_RECURRING_PAYMENTS;
$agreement['L_BILLINGAGREEMENTDESCRIPTION0'] = 'Insert some description here';
$agreement['NOSHIPPING'] = 1;
$storage->update($agreement);

$captureToken = $payum->getTokenFactory->createCaptureToken('paypal', $agreement, 'create_recurring_payment.php');

$storage->update($agreement);

header('Location: ' .$captureToken->getTargetUrl());
