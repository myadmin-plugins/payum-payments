# MyAdmin Payum Payments Plugin

Payum payment gateway integration for the MyAdmin hosting control panel. Namespace: `Detain\MyAdminPayum\` → `src/`.

## Commands

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpunit tests/
```

## Architecture

**Entry**: `src/Plugin.php` (`Detain\MyAdminPayum\Plugin`) — static methods `getHooks()`, `getMenu(GenericEvent)`, `getRequirements(GenericEvent)`, `getSettings(GenericEvent)`. Registered via Symfony `EventDispatcher`.

**Gateways**:
- PayPal Express Checkout NVP: `src/paypal_express_checkout_nvp/config.php` · `capture.php` · `prepare.php` · `done.php`
- Stripe.js: `src/getting_started/5_stripe_js_create_gateway.php` → `8_stripe_js_credit_card.php`
- PayPal IPN verification: `src/paypal_ipn.php`
- Token customization: `src/authorize_token_custom_query_parameter.txt`

**Recurring Payments** (`src/recurring_payments/`):
- Models: `AgreementDetails.php` · `RecurringPaymentDetais.php` (both `extends \ArrayObject`, namespace `App\Model`)
- Scripts: `prepare.php` → `create_recurring_payment.php` → `cancel_recurring_payment.php`
- Config: `config.php` uses `FilesystemStorage` for both model classes

**Storage Backends** (`src/storages/`):
- Doctrine ORM: `doctrine_orm/Payment.php` · `doctrine_orm/PaymentToken.php` · `doctrine_orm/entity_manager_and_payum_storage.php`
- MongoDB ODM: `doctrine_mongoodm/Payment.php` · `doctrine_mongoodm/PaymentToken.php` · `doctrine_mongoodm/entity_manager_and_payum_storage.php`
- Filesystem: `filesystem/entity_manager_and_payum_storage.php`
- Propel2: `propel2/connection.php` · `propel2/custom_storage.php` (implements `StorageInterface`)
- Usage patterns: `storages/explicityly_used.php` · `storages/implicitly_used.php` · `storages/model-identity_with_extension.php`

**Logger** (`src/logger/`):
- `LoggerAwareAction.php` implements `ActionInterface` + `LoggerAwareInterface` (PSR-3), guards with `if ($this->logger)`
- `debugging.php` — `LogExecutedActionsExtension` pattern
- `logger_Extension_when_new_gateway.php` — `LoggerExtension` pattern

**Tests** (`tests/`): PHPUnit 9 · config `phpunit.xml.dist` · `Detain\MyAdminPayum\Tests\` → `tests/`

## Key Patterns

**Gateway config** (`src/paypal_express_checkout_nvp/config.php`):
```php
$payum = (new PayumBuilder())
    ->addDefaultStorages()
    ->addGateway('gatewayName', [
        'factory' => 'paypal_express_checkout',
        'username' => 'change it', 'password' => 'change it',
        'signature' => 'change it', 'sandbox' => true
    ])
    ->getPayum();
```

**Capture flow** (`src/paypal_express_checkout_nvp/capture.php`):
```php
if ($reply = $gateway->execute(new Capture($token), true)) {
    if ($reply instanceof HttpRedirect) { header('Location: ' .$reply->getUrl()); die(); }
    throw new \LogicException('Unsupported reply', null, $reply);
}
$payum->getRequestVerifier()->invalidate($token);
header('Location: ' .$token->getAfterUrl());
```

**Status check** (`src/paypal_express_checkout_nvp/done.php` / `src/getting_started/4_get_gateway.php`):
```php
$gateway->execute($status = new GetHumanStatus($token));
$status->isCaptured(); $status->isCanceled(); $status->isFailed(); $status->getValue();
```

**Doctrine ORM storage model** (`src/storages/doctrine_orm/Payment.php`):
```php
namespace Acme\Entity;
use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;
class Payment extends BasePayment { protected $id; }
```

**MongoDB ODM storage model** (`src/storages/doctrine_mongoodm/Payment.php`):
```php
namespace Acme\Document;
use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Payum\Core\Model\Payment as BasePayment;
class Payment extends BasePayment { protected $id; }
```

**Logger-aware action** (`src/logger/LoggerAwareAction.php`):
```php
namespace App\Payum\Action;
class LoggerAwareAction implements ActionInterface, LoggerAwareInterface {
    protected $logger;
    public function setLogger(LoggerInterface $logger) { $this->logger = $logger; }
    public function execute($request) {
        if ($this->logger) { $this->logger->debug('message'); }
    }
}
```

## Conventions

- PSR-4: `Detain\MyAdminPayum\` → `src/` · `Detain\MyAdminPayum\Tests\` → `tests/`
- Storage payment models: extend `Payum\Core\Model\Payment as BasePayment` with `protected $id`
- Storage token models: extend `Payum\Core\Model\Token`
- Recurring models: extend `\ArrayObject` · namespace `App\Model`
- Token models: `PaymentToken` in same namespace as `Payment` (ORM: `Acme\Entity`, ODM: `Acme\Document`)
- Sensitive data: use `SensitiveValue` (`src/sensitive_info.php`) — never log raw card numbers
- Credentials never hardcoded — use constants (`PAYPAL_API_USERNAME` etc.)
- All PHP files start with `<?php`
- Tabs for indentation (`.scrutinizer.yml` coding style)
- Commit messages: lowercase, descriptive

## Testing Conventions

All tests extend `PHPUnit\Framework\TestCase`, declare `strict_types=1`, namespace `Detain\MyAdminPayum\Tests`.

Pattern — file existence + content assertion:
```php
public function testFileExists(): void {
    $this->assertFileExists(__DIR__ . '/../src/path/file.php');
}
public function testContainsClass(): void {
    $contents = file_get_contents(__DIR__ . '/../src/path/file.php');
    $this->assertStringContainsString('ClassName', $contents);
}
```

Test files: `tests/FileExistenceTest.php` · `tests/PluginTest.php` · `tests/GettingStartedScriptsTest.php` · `tests/PaypalExpressCheckoutScriptsTest.php` · `tests/RecurringPaymentScriptsTest.php` · `tests/RecurringPaymentModelsTest.php` · `tests/StorageModelsTest.php` · `tests/StorageScriptsTest.php` · `tests/LoggerAwareActionTest.php`

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md .agents/ .opencode/ 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->

<!-- caliber:managed:sync -->
## Context Sync

This project uses [Caliber](https://github.com/caliber-ai-org/ai-setup) to keep AI agent configs in sync across Claude Code, Cursor, Copilot, and Codex.
Configs update automatically before each commit via `caliber refresh`.
If the pre-commit hook is not set up, run `/setup-caliber` to configure everything automatically.
<!-- /caliber:managed:sync -->
