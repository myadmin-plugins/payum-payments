# Webuzo handling plugin for MyAdmin

Webuzo handling plugin for MyAdmin

## Links

* [Docs](https://github.com/Payum/Payum/blob/master/docs/index.md)
* [Get it started](https://github.com/Payum/Payum/blob/master/docs/get-it-started.md)
* [Architecture](https://github.com/Payum/Payum/blob/master/docs/the-architecture.md)
* [Storages](https://github.com/Payum/Payum/blob/master/docs/storages.md)
* [Gateways](https://github.com/Payum/Payum/blob/master/docs/supported-gateways.md)
* [Debugging](https://github.com/Payum/Payum/blob/master/docs/debugging.md)
* [Logger](https://github.com/Payum/Payum/blob/master/docs/logger.md)
* [Gateway in Backend](https://github.com/Payum/Payum/blob/master/docs/configure-gateway-in-backend.md)
* [Paypal Express Checkout - Get it Started](https://github.com/Payum/Payum/blob/master/docs/paypal/express-checkout/get-it-started.md)
* [Payum tagged StackOverflow Q&A](https://stackoverflow.com/questions/tagged/payum)



## Gateways

### Official

Paypal Express Checkout, Paypal Pro Checkout, Paypal Pro Hosted, Paypal Masspay, Paypal Rest, Stripe.js, Stripe Checkout, Authorize.Net AIM, Be2Bill CreditCard, Be2Bill Offsite, Payex, Offline, Klarna Checkout, Klarna Invoice, Sofort

### Community

r3s7/PayumDineroMail, r3s7/PayumBitPay, kayue/Paydollar, locastic/PayumPaylinkJustpay, locastic/TcomPayWayPayum, LedjIn/Sagepay, crevillo/payum-redsys, invit/payum-sofortueberweisung (abandoned), wiseape/payum-sofortuberweisung (abandoned), wiseape/payum-paymill, gdaws/payum_braintree, peterfox/Payum-Bitpay, winzou/payum-limonetik, ekipower/payum-nganluong, tmconsulting/payum-uniteller-gateway (^1.4), fullpipe/payum-uniteller (0.14.*), fullpipe/payum-flexidengi, paradigm/payum-trustly, paradigm/payum-paytrail, ekyna/PayumSips, accesto/PayumPayU, pixers/payum-dotpay, khal3d/payum-cashnpay, NetTeam/payum-adyen, NetTeam/payum-paymill, BoShurik/payum-yandex-money, sergeym/payum-pay-receipt, remyma/payum-paybox, artkonekt/payum-otp-hungary, sourcefabric/payum-mollie, sourcefabric/payum-mbe4, valiton/payum-payone, Setono/payum-quickpay

### Omnipay gateways.

thephpleague/omnipay-2checkout, lokielse/omnipay-alipay, thephpleague/omnipay-authorizenet, thephpleague/omnipay-buckaroo, thephpleague/omnipay-cardsave, thephpleague/omnipay-coinbase, dioscouri/omnipay-cybersource, DABSquared/omnipay-cybersource-soap, coatesap/omnipay-datacash, thephpleague/omnipay-dummy, dercoder/omnipay-ecopayz, thephpleague/omnipay-eway, thephpleague/omnipay-firstdata, thephpleague/omnipay-gocardless, thephpleague/omnipay-manual, thephpleague/omnipay-migs, thephpleague/omnipay-mollie, thephpleague/omnipay-multisafepay, thephpleague/omnipay-netaxept, thephpleague/omnipay-netbanx, alfaproject/omnipay-neteller, mfauveau/omnipay-pacnet, thephpleague/omnipay-payfast, thephpleague/omnipay-payflow, thephpleague/omnipay-paymentexpress, coatesap/omnipay-paymentsense, thephpleague/omnipay-paypal, efesaid/omnipay-payu, thephpleague/omnipay-pin, coatesap/omnipay-realex, thephpleague/omnipay-sagepay, thephpleague/omnipay-securepay, justinbusschau/omnipay-secpay, fruitcakestudio/omnipay-sisow, alfaproject/omnipay-skrill, thephpleague/omnipay-stripe, thephpleague/omnipay-targetpay, thephpleague/omnipay-worldpay, aTastyCookie/yandexmoney_omnipay, antqa/payum-perfectmoney

### JMS payment plugins

Paypal Express Checkout, Slim CD, Be2bill, Qiwi Wallet, Robokassa, Mtgox, Saferpay, Virgopass, Atos SIPS, Dotpay, Ogone, MeS, Adyen

## Scripts

These scripts shows you how to fill the gap betwen an http request that comes to you,  a refund for example and the payum gateway. It is expected that you copy\past the script to your code and reuse it for all gateways without changes.



## Build Status and Code Analysis

Site          | Status
--------------|---------------------------
![Travis-CI](http://i.is.cc/storage/GYd75qN.png "Travis-CI")     | [![Build Status](https://travis-ci.org/detain/myadmin-payum-payments.svg?branch=master)](https://travis-ci.org/detain/myadmin-payum-payments)
![CodeClimate](http://i.is.cc/storage/GYlageh.png "CodeClimate")  | [![Code Climate](https://codeclimate.com/github/detain/myadmin-payum-payments/badges/gpa.svg)](https://codeclimate.com/github/detain/myadmin-payum-payments) [![Test Coverage](https://codeclimate.com/github/detain/myadmin-payum-payments/badges/coverage.svg)](https://codeclimate.com/github/detain/myadmin-payum-payments/coverage) [![Issue Count](https://codeclimate.com/github/detain/myadmin-payum-payments/badges/issue_count.svg)](https://codeclimate.com/github/detain/myadmin-payum-payments)
![Scrutinizer](http://i.is.cc/storage/GYeUnux.png "Scrutinizer")   | [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/myadmin-plugins/payum-payments/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/myadmin-plugins/payum-payments/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/myadmin-plugins/payum-payments/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/myadmin-plugins/payum-payments/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/myadmin-plugins/payum-payments/badges/build.png?b=master)](https://scrutinizer-ci.com/g/myadmin-plugins/payum-payments/build-status/master)
![Codacy](http://i.is.cc/storage/GYi66Cx.png "Codacy")        | [![Codacy Badge](https://api.codacy.com/project/badge/Grade/226251fc068f4fd5b4b4ef9a40011d06)](https://www.codacy.com/app/detain/myadmin-payum-payments) [![Codacy Badge](https://api.codacy.com/project/badge/Coverage/25fa74eb74c947bf969602fcfe87e349)](https://www.codacy.com/app/detain/myadmin-payum-payments?utm_source=github.com&utm_medium=referral&utm_content=detain/myadmin-payum-payments&utm_campaign=Badge_Coverage)
![Coveralls](http://i.is.cc/storage/GYjNSim.png "Coveralls")    | [![Coverage Status](https://coveralls.io/repos/github/detain/db_abstraction/badge.svg?branch=master)](https://coveralls.io/github/detain/myadmin-payum-payments?branch=master)
![Packagist](http://i.is.cc/storage/GYacBEX.png "Packagist")     | [![Latest Stable Version](https://poser.pugx.org/detain/myadmin-payum-payments/version)](https://packagist.org/packages/detain/myadmin-payum-payments) [![Total Downloads](https://poser.pugx.org/detain/myadmin-payum-payments/downloads)](https://packagist.org/packages/detain/myadmin-payum-payments) [![Latest Unstable Version](https://poser.pugx.org/detain/myadmin-payum-payments/v/unstable)](//packagist.org/packages/detain/myadmin-payum-payments) [![Monthly Downloads](https://poser.pugx.org/detain/myadmin-payum-payments/d/monthly)](https://packagist.org/packages/detain/myadmin-payum-payments) [![Daily Downloads](https://poser.pugx.org/detain/myadmin-payum-payments/d/daily)](https://packagist.org/packages/detain/myadmin-payum-payments) [![License](https://poser.pugx.org/detain/myadmin-payum-payments/license)](https://packagist.org/packages/detain/myadmin-payum-payments)


## Installation

Install with composer like

```sh
composer require detain/myadmin-payum-payments
```

## License

The Webuzo handling plugin for MyAdmin class is licensed under the LGPL-v2.1 license.

