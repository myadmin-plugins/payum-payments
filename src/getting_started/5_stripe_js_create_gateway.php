<?php

use Payum\Stripe\StripeJsGatewayFactory;

$factory = new StripeJsGatewayFactory();

$gateway = $factory->create(
	[
	'publishable_key' => 'aKey',
	'secret_key' => 'aKey'
	]
);
