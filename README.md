# MyAdmin Payum Payments

[![Build Status](https://github.com/detain/myadmin-payum-payments/actions/workflows/tests.yml/badge.svg)](https://github.com/detain/myadmin-payum-payments/actions/workflows/tests.yml)
[![Latest Stable Version](https://poser.pugx.org/detain/myadmin-payum-payments/version)](https://packagist.org/packages/detain/myadmin-payum-payments)
[![Total Downloads](https://poser.pugx.org/detain/myadmin-payum-payments/downloads)](https://packagist.org/packages/detain/myadmin-payum-payments)
[![License](https://poser.pugx.org/detain/myadmin-payum-payments/license)](https://packagist.org/packages/detain/myadmin-payum-payments)

Payum payment gateway integration plugin for the MyAdmin hosting control panel. Provides a unified interface for processing payments through multiple providers including PayPal Express Checkout NVP, Stripe.js, and other Payum-supported gateways. Includes support for one-time captures, recurring billing profiles, and multiple storage backends (Doctrine ORM, MongoDB ODM, Filesystem, Propel2).

## Features

- PayPal Express Checkout NVP integration with capture, redirect handling, and IPN verification
- Stripe.js gateway support with credit card and token-based payments
- Recurring payment management (create, sync, cancel billing profiles)
- Multiple storage backends: Doctrine ORM, MongoDB ODM, Filesystem, Propel2
- Symfony EventDispatcher-based plugin hook system
- Logger-aware action pattern with PSR-3 compatibility

## Requirements

- PHP 8.2 or higher
- ext-soap

## Installation

Install with Composer:

```sh
composer require detain/myadmin-payum-payments
```

## Usage

The plugin registers itself through the MyAdmin plugin system via Symfony EventDispatcher hooks. The `Plugin` class provides static methods for menu integration, requirements loading, and settings management.

## Testing

Run the test suite with PHPUnit:

```sh
composer install
vendor/bin/phpunit
```

## License

This package is licensed under the LGPL-2.1 license.
