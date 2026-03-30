---
name: recurring-payment-flow
description: Codifies the PayPal recurring billing lifecycle using Payum: AgreementDetails model → prepare (capture token) → create_recurring_payment (isCaptured guard + CreateRecurringPaymentProfile + Sync + doneToken redirect) → cancel flow. Use when user says 'recurring payment', 'billing profile', 'subscription', 'recurring setup', or modifies src/recurring_payments/. Do NOT use for one-time capture flows or non-PayPal gateways.
---
# Recurring Payment Flow

## Critical

- **Always guard `src/recurring_payments/create_recurring_payment.php` with `isCaptured()`** — if the agreement is not captured, return HTTP 400 immediately and exit. Never proceed to `CreateRecurringPaymentProfile` without this check.
- **Invalidate the token immediately** after `verify()` in `src/recurring_payments/create_recurring_payment.php` — call `$payum->getRequestVerifier()->invalidate($token)` before any other logic.
- **Model filename typo is intentional**: the file is `src/recurring_payments/RecurringPaymentDetais.php` (missing an 'l') — do not rename it; tests reference this exact filename.
- Both model classes live in namespace `App\Model`, not `Detain\MyAdminPayum`.
- Storage for both models must use `FilesystemStorage` and be added to the `$storages` array in `src/recurring_payments/config.php`.

## Instructions

### Step 1 — Define model classes

Create `src/recurring_payments/AgreementDetails.php`:
```php
<?php

namespace App\Model;

use Payum\Core\Model\ArrayObject;

/**
 * {@inheritDoc}
 */

/**
 * Class AgreementDetails
 *
 * @package App\Model
 */
class AgreementDetails extends \ArrayObject
{
}
```

Create `src/recurring_payments/RecurringPaymentDetais.php` (note: 'Detais', not 'Details'):
```php
<?php

namespace App\Model;

use Payum\Core\Model\ArrayObject;

/**
 * {@inheritDoc}
 */

/**
 * Class RecurringPaymentDetails
 *
 * @package App\Model
 */
class RecurringPaymentDetails extends \ArrayObject
{
}
```

Verify: both files exist under `src/recurring_payments/`, both contain `extends \ArrayObject`, both declare `namespace App\Model`.

### Step 2 — Configure storage in `src/recurring_payments/config.php`

`src/recurring_payments/config.php` must register `FilesystemStorage` for both model classes into the `$storages` array:
```php
<?php

//config.php

$agreementClass = 'App\Model\AgreementDetails';
$recurringPaymentClass = 'App\Model\RecurringPaymentDetails';

$storages[$agreementClass] = new FilesystemStorage(
    __DIR__.'/storage',
    $agreementClass
);
$storages[$recurringPaymentClass] = new FilesystemStorage(
    __DIR__.'/storage',
    $recurringPaymentClass
);
```

Verify: `$storages[` appears twice, once per class. The `storage/` subdirectory must be writable at runtime.

### Step 3 — Build `src/recurring_payments/prepare.php` (agreement setup + capture token)

`src/recurring_payments/prepare.php` creates the agreement, sets billing fields, and redirects to the capture URL:
```php
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

$captureToken = $payum->getTokenFactory()->createCaptureToken('paypal', $agreement, 'create_recurring_payment.php');

$storage->update($agreement);

header('Location: ' .$captureToken->getTargetUrl());
```

Key fields:
- `PAYMENTREQUEST_0_AMT` must be `0` for a billing agreement (not the recurring charge amount).
- `L_BILLINGTYPE0` must be `Api::BILLINGTYPE_RECURRING_PAYMENTS`.
- The after-URL passed to `createCaptureToken` is `'create_recurring_payment.php'`.

Verify: script contains `BILLINGTYPE_RECURRING_PAYMENTS`, `L_BILLINGAGREEMENTDESCRIPTION0`, and `createCaptureToken`.

### Step 4 — Build `src/recurring_payments/create_recurring_payment.php` (profile creation + sync + redirect)

This step depends on the agreement token produced in Step 3.

```php
<?php

// create_recurring_payment.php

use Payum\Core\Request\Sync;
use Payum\Core\Request\GetHumanStatus;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfile;

include 'config.php';

$token = $payum->getRequestVerifier()->verify($_REQUEST);
$payum->getRequestVerifier()->invalidate($token);          // invalidate before any other work

$gateway = $payum->getGateway($token->getGatewayName());

$agreementStatus = new GetHumanStatus($token);
$gateway->execute($agreementStatus);

if (!$agreementStatus->isCaptured()) {                     // MUST guard here
    header('HTTP/1.1 400 Bad Request', true, 400);
    exit;
}

$agreement = $agreementStatus->getModel();

$storage = $payum->getStorage($recurringPaymentClass);

$recurringPayment = $storage->create();
$recurringPayment['TOKEN']            = $agreement['TOKEN'];
$recurringPayment['DESC']             = 'Your subscription description.';
$recurringPayment['EMAIL']            = $agreement['EMAIL'];
$recurringPayment['AMT']              = 0.05;               // recurring charge per period
$recurringPayment['CURRENCYCODE']     = 'USD';
$recurringPayment['BILLINGFREQUENCY'] = 7;
$recurringPayment['PROFILESTARTDATE'] = date(DATE_ATOM);
$recurringPayment['BILLINGPERIOD']    = Api::BILLINGPERIOD_DAY;

$gateway->execute(new CreateRecurringPaymentProfile($recurringPayment));
$gateway->execute(new Sync($recurringPayment));             // sync after profile creation

$doneToken = $payum->getTokenFactory()->createToken('paypal', $recurringPayment, 'done.php');

header('Location: ' .$doneToken->getTargetUrl());
```

Required `$recurringPayment` fields: `TOKEN`, `DESC`, `EMAIL`, `AMT`, `CURRENCYCODE`, `BILLINGFREQUENCY`, `PROFILESTARTDATE`, `BILLINGPERIOD`.

Verify: script contains `isCaptured`, `400 Bad Request`, `CreateRecurringPaymentProfile`, `Sync`, and redirect to `$doneToken->getTargetUrl()`.

### Step 5 — Build `src/recurring_payments/cancel_recurring_payment.php`

Assumes `$gateway` and `$recurringPayment` are already in scope (loaded from storage upstream):
```php
<?php

use Payum\Core\Request\Cancel;
use Payum\Core\Request\Sync;
use Payum\Core\Request\GetHumanStatus;

$gateway->execute(new Cancel($recurringPayment));
$gateway->execute(new Sync($recurringPayment));             // sync after cancel

$gateway->execute($status = new GetHumanStatus($recurringPayment));

if ($status->isCanceled()) {
    // cancellation confirmed
} else {
    // cancellation pending or failed
}
```

Verify: script uses `Cancel`, `Sync`, `GetHumanStatus`, and checks `isCanceled()`.

### Step 6 — Run tests

```bash
vendor/bin/phpunit tests/RecurringPaymentModelsTest.php tests/RecurringPaymentScriptsTest.php
```

All assertions in both test files must pass before considering the flow complete.

## Examples

**User says:** "Add a weekly $1 subscription using PayPal recurring payments"

**Actions taken:**
1. Create `src/recurring_payments/AgreementDetails.php` and `src/recurring_payments/RecurringPaymentDetais.php` (both `extends \ArrayObject`, namespace `App\Model`).
2. In `src/recurring_payments/config.php`, add `FilesystemStorage` entries for both classes.
3. In `src/recurring_payments/prepare.php`, set `PAYMENTREQUEST_0_AMT = 0`, `L_BILLINGTYPE0 = Api::BILLINGTYPE_RECURRING_PAYMENTS`, description, `NOSHIPPING = 1`, then `createCaptureToken('paypal', $agreement, 'create_recurring_payment.php')`.
4. In `src/recurring_payments/create_recurring_payment.php`, verify + invalidate token, check `isCaptured()` (400 on failure), populate `$recurringPayment` with `AMT = 1.00`, `BILLINGFREQUENCY = 7`, `BILLINGPERIOD = Api::BILLINGPERIOD_DAY`, execute `CreateRecurringPaymentProfile` then `Sync`, redirect to `doneToken`.
5. In `src/recurring_payments/cancel_recurring_payment.php`, execute `Cancel` then `Sync`, check `isCanceled()`.

**Result:** Five files in `src/recurring_payments/` matching the exact pattern above; all `RecurringPaymentModelsTest` and `RecurringPaymentScriptsTest` assertions pass.

## Common Issues

**`isCaptured()` returns false / gets HTTP 400:**
- The PayPal Express Checkout agreement was not completed by the user (they cancelled or the token expired).
- Verify `src/recurring_payments/prepare.php` sets `L_BILLINGTYPE0 = Api::BILLINGTYPE_RECURRING_PAYMENTS` — any other billing type will prevent capture.
- Verify `PAYMENTREQUEST_0_AMT` is `0` in `src/recurring_payments/prepare.php`; a non-zero amount here causes the initial express checkout to behave as a sale, not an agreement.

**`Class 'App\Model\AgreementDetails' not found` during storage lookup:**
- The FQCNs in `src/recurring_payments/config.php` (`$agreementClass = 'App\Model\AgreementDetails'`) must match the `namespace` + `class` declarations in the model files exactly.
- Run `grep -r 'namespace' src/recurring_payments/*.php` to confirm both files declare `namespace App\Model`.

**`RecurringPaymentDetais.php` test assertion fails (`assertFileExists`):**
- The filename is intentionally `src/recurring_payments/RecurringPaymentDetais.php` (missing 'l'). Do not create a corrected `RecurringPaymentDetails.php` — the test checks the misspelled name.

**`CreateRecurringPaymentProfile` request fails at PayPal:**
- All eight required fields must be present: `TOKEN`, `DESC`, `EMAIL`, `AMT`, `CURRENCYCODE`, `BILLINGFREQUENCY`, `PROFILESTARTDATE`, `BILLINGPERIOD`.
- `PROFILESTARTDATE` must be ISO 8601 format — use `date(DATE_ATOM)`.
- `TOKEN` must be copied from `$agreement['TOKEN']` (not the Payum security token).

**`Sync` after `Cancel` still shows active status:**
- PayPal may take a moment to reflect cancellation. Check `isCanceled()` strictly — do not assume success if it returns false; surface an error or re-queue instead of silently continuing.

**`getTokenFactory` call fails (`Call to undefined method`):**
- Use `$payum->getTokenFactory()->createCaptureToken(...)` — note `getTokenFactory()` is a method call, not a property. The source file `src/recurring_payments/prepare.php` has a known typo `$payum->getTokenFactory->createCaptureToken` (missing parentheses); correct this when writing new code.
