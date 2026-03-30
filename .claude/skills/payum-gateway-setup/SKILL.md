---
name: payum-gateway-setup
description: Codifies the PayumBuilder + gateway factory + capture/prepare/done/config flow for adding new payment gateways. Use when user says 'add gateway', 'new payment provider', 'configure paypal', 'set up stripe', or adds files to `src/paypal_express_checkout_nvp/` or `src/getting_started/`. Key capabilities: `PayumBuilder->addGateway()`, `Capture` execution, `GetHumanStatus`, token invalidation pattern. Do NOT use for recurring payment setup (use payum-recurring-payment skill instead).
---
# Payum Gateway Setup

## Critical

- Every gateway **must** have exactly four files in its directory: `config.php`, `prepare.php`, `capture.php`, `done.php`.
- Never store credentials in source files — use constants (e.g. `PAYPAL_API_USERNAME`) loaded from the MyAdmin bootstrap.
- Always invalidate the capture token after successful payment via `$payum->getRequestVerifier()->invalidate($token)`.
- The `done.php` response **must** call `GetHumanStatus` and return JSON — never redirect silently on completion.
- Verify `$reply instanceof HttpRedirect` (PayPal-style) or `$reply instanceof HttpResponse` (Stripe-style) — they are mutually exclusive by gateway type.

## Instructions

### Step 1 — Create the gateway directory and config.php

Create `config.php` in the new gateway directory using `PayumBuilder`. Use `addDefaultStorages()` for filesystem-backed tokens/payments unless a custom storage backend is needed.

```php
<?php
// config.php
use Payum\Core\PayumBuilder;
use Payum\Core\Payum;

/** @var Payum $payum */
$payum = (new PayumBuilder())
    ->addDefaultStorages()
    ->addGateway('gatewayName', [
        'factory'  => 'factory_name',   // e.g. 'paypal_express_checkout', 'stripe_js'
        'username' => GATEWAY_USERNAME, // use constants, never literals
        'password' => GATEWAY_PASSWORD,
        'sandbox'  => true              // flip to false for production
    ])
    ->getPayum()
;
```

**Verify:** `$gatewayName` string matches the key used in `addGateway()` — this same string is passed to `createCaptureToken()` in `prepare.php`.

### Step 2 — Create `prepare.php`

This script creates a payment model, stores it, and redirects the user to the gateway's capture URL.

```php
<?php
// prepare.php
include 'config.php';
$gatewayName  = 'gatewayName'; // must match addGateway() key from config.php
$storage  = $payum->getStorage($paymentClass);
$payment  = $storage->create();
$payment->setNumber(uniqid('', true));
$payment->setCurrencyCode('USD');
$payment->setTotalAmount(123);           // integer cents — 123 = $1.23
$payment->setDescription('A description');
$payment->setClientId('anId');
$payment->setClientEmail('foo@example.com');
$payment->setDetails([]);                // gateway-specific extra fields go here
$storage->update($payment);
$captureToken = $payum->getTokenFactory()->createCaptureToken($gatewayName, $payment, 'done.php');
header('Location: ' . $captureToken->getTargetUrl());
```

**Verify:** `done.php` (the third argument to `createCaptureToken`) exists in the same directory before testing this flow.

### Step 3 — Create `capture.php`

Handle the gateway reply after the user returns. Reply type differs by gateway:
- **Redirect gateways** (PayPal Express Checkout): handle `HttpRedirect`.
- **Form/JS gateways** (Stripe.js): handle `HttpResponse`.

**Redirect gateway (PayPal-style):**
```php
<?php
// capture.php
include 'config.php';
$payum->getHttpController()->capture();
if ($reply = $gateway->execute(new Capture($token), true)) {
    if ($reply instanceof HttpRedirect) {
        header('Location: ' . $reply->getUrl());
        die();
    }
    throw new \LogicException('Unsupported reply', null, $reply);
}
$payum->getRequestVerifier()->invalidate($token);
header('Location: ' . $token->getAfterUrl());
```

**Form/JS gateway (Stripe-style):**
```php
<?php
// capture.php
try {
    $gateway->execute(new \Payum\Core\Request\Capture($model));
} catch (Payum\Core\Reply\ReplyInterface $reply) {
    if ($reply instanceof Payum\Core\Reply\HttpResponse) {
        echo $reply->getContent();
        exit;
    }
    throw new \LogicException('Unsupported reply', null, $reply);
}
```

**Verify:** Gateway reply class (`HttpRedirect` vs `HttpResponse`) matches the gateway factory used in `config.php`.

### Step 4 — Create `done.php`

Verify the token, execute `GetHumanStatus`, and return a JSON response. Always respond with `application/json`.

```php
<?php
// done.php
use Payum\Core\Request\GetHumanStatus;

include 'config.php';
$token   = $payum->getRequestVerifier()->verify($_REQUEST);
$gateway = $payum->getGateway($token->getGatewayName());
// Optionally invalidate so the URL cannot be reused:
// $payum->getRequestVerifier()->invalidate($token);
$gateway->execute($status = new GetHumanStatus($token));
$payment = $status->getFirstModel();
header('Content-Type: application/json; charset=UTF-8');
echo json_encode([
    'status' => $status->getValue(),   // 'new','pending','authorized','captured','failed', etc.
    'order'  => [
        'total_amount'  => $payment->getTotalAmount(),
        'currency_code' => $payment->getCurrencyCode(),
        'details'       => $payment->getDetails(),
    ],
]);
```

**Verify:** `$status->getValue()` returns one of: `new`, `pending`, `authorized`, `captured`, `refunded`, `canceled`, `suspended`, `failed`, `unknown`.

### Step 5 — Register the factory constant file

If the gateway uses factory-level credentials, add constants to `src/sensitive_info.php` (already `.gitignore`-friendly) and ensure credentials are loaded via the MyAdmin bootstrap when running in production.

**Verify:** No raw credential strings appear in any committed `.php` file.

### Step 6 — Add PHPUnit test

Add a test file under `tests/` that mirrors existing tests (see `tests/PaypalExpressCheckoutScriptsTest.php` for the pattern). At minimum, assert all four files exist and are valid PHP.

Run: `vendor/bin/phpunit tests/`

**Verify:** All tests pass before opening a PR.

## Examples

**User says:** "Add a Stripe gateway"

**Actions taken:**
1. Create `src/stripe_js/config.php` — `addGateway('stripe_js', ['factory' => 'stripe_js', 'publishable_key' => STRIPE_PUB_KEY, 'secret_key' => STRIPE_SECRET_KEY])`
2. Create `src/stripe_js/prepare.php` — build `Payment` model with `setCurrencyCode('USD')`, `setTotalAmount(100)`, call `createCaptureToken('stripe_js', $payment, 'done.php')`
3. Create `src/stripe_js/capture.php` — use `HttpResponse` variant (Stripe renders a form, not a redirect)
4. Create `src/stripe_js/done.php` — standard `GetHumanStatus` → JSON response
5. Add constants `STRIPE_PUB_KEY`, `STRIPE_SECRET_KEY` to `src/sensitive_info.php`
6. Add `tests/StripeJsScriptsTest.php` asserting file existence

**Result:** Four files matching the exact structure in `src/paypal_express_checkout_nvp/`, `vendor/bin/phpunit` green.

## Common Issues

**`LogicException: Unsupported reply`**
You used the `HttpRedirect` branch for a form-based gateway (or vice versa).
Fix: Check the gateway type — Stripe.js throws `HttpResponse`, PayPal Express throws `HttpRedirect`. Swap the `instanceof` check in `capture.php`.

**`InvalidArgumentException: Gateway "gatewayName" does not exist`**
The string passed to `$payum->getGateway()` or `createCaptureToken()` does not match the key in `addGateway()`.
Fix: Search `config.php` for the `addGateway` first argument and use that exact string everywhere.

**`TotalAmount must be an integer`**
You passed a float (`1.23`) instead of cents (`123`) to `setTotalAmount()`.
Fix: Always pass integer cents: `$payment->setTotalAmount((int) round($amount * 100))`.

**Token verification fails / 404 on `done.php`**
The `done.php` filename passed to `createCaptureToken()` doesn't match the actual filename, or the file is in a different directory.
Fix: The third argument must be a URL or filename relative to the web root that resolves to `done.php` in the same directory. Confirm the file exists at that path.

**`Class 'Payum\Paypal\ExpressCheckout\PaypalExpressCheckoutGatewayFactory' not found`**
The gateway-specific Composer package is not installed.
Fix: Run `composer require payum/paypal-express-checkout-nvp` (or the relevant gateway package) then `composer install`.
