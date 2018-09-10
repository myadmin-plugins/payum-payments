<?php
// done.php
use Payum\Core\Request\GetHumanStatus;

include 'config.php';
$token = $payum->getRequestVerifier()->verify($_REQUEST);
$gateway = $payum->getGateway($token->getGatewayName());
// you can invalidate the token. The url could not be requested any more.
// $payum->getRequestVerifier()->invalidate($token);
// Once you have token you can get the model from the storage directly.
//$identity = $token->getDetails();
//$payment = $payum->getStorage($identity->getClass())->find($identity);
// or Payum can fetch the model for you while executing a request (Preferred).
$gateway->execute($status = new GetHumanStatus($token));
$payment = $status->getFirstModel();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(
	[
		'status' => $status->getValue(),
		'order' => [
		'total_amount' => $payment->getTotalAmount(),
		'currency_code' => $payment->getCurrencyCode(),
		'details' => $payment->getDetails()
		]
	]
);
