<?php
// create_recurring_payment.php

use Payum\Core\Request\Sync;
use Payum\Core\Request\GetHumanStatus;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfile;

include 'config.php';

$token = $payum->getRequestVerifier()->verify($_REQUEST);
$payum->getRequestVerifier()->invalidate($token);

$gateway = $payum->getGateway($token->getGatewayName());

$agreementStatus = new GetHumanStatus($token);
$gateway->execute($agreementStatus);

if (!$agreementStatus->isCaptured()) {
    header('HTTP/1.1 400 Bad Request', true, 400);
    exit;
}

$agreement = $agreementStatus->getModel();

$storage = $payum->getStorage($recurringPaymentClass);

$recurringPayment = $storage->create();
$recurringPayment['TOKEN'] = $agreement['TOKEN'];
$recurringPayment['DESC'] = 'Subscribe to weather forecast for a week. It is 0.05$ per day.';
$recurringPayment['EMAIL'] = $agreement['EMAIL'];
$recurringPayment['AMT'] = 0.05;
$recurringPayment['CURRENCYCODE'] = 'USD';
$recurringPayment['BILLINGFREQUENCY'] = 7;
$recurringPayment['PROFILESTARTDATE'] = date(DATE_ATOM);
$recurringPayment['BILLINGPERIOD'] = Api::BILLINGPERIOD_DAY;

$gateway->execute(new CreateRecurringPaymentProfile($recurringPayment));
$gateway->execute(new Sync($recurringPayment));

$doneToken = $payum->geTokenFactory()->createToken('paypal', $recurringPayment, 'done.php');

header('Location: ' .$doneToken->getTargetUrl());
