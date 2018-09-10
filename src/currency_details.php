Payum provides ability to get currency details listed in ISO4217 specification. To get this information you have to execute a GetCurrency request with a currency code.
<?php

$factory = new \Payum\Offline\OfflineGatewayFactory();
$gateway = $factory->create();

$gateway->execute($currency = new \Payum\Core\GetCurrency('USD'));

echo $currency->alpha3;  // USD
echo $currency->name;    // US Dollar
echo $currency->exp;     // 2
echo $currency->country; // US

// and so on...
?>
Or inside another action:
<?php

/**
 * Class FooAction
 */
class FooAction extends GatewayAwareAction
{
	/**
	 * @param $request
	 */
	public function execute($request)
	{
		$this->gateway->execute($currency = new \Payum\Core\GetCurrency('USD'));

		echo $currency->alpha3;  // USD
		echo $currency->name;    // US Dollar
		echo $currency->exp;     // 2
		echo $currency->country; // US
	}
}
?>
Or directly ISO4217 service:
<?php

$iso4217 = new \Payum\ISO4216\ISO4217();

/** @var \Payum\ISO4216\Currency $currency **/
$currency = $iso4217->findByAlpha3('USD');

echo $currency->getAlpha3();  // USD
echo $currency->getName();    // US Dollar
echo $currency->getExp();     // 2
echo $currency->getCountry(); // US
?>
