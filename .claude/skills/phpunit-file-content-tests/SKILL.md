---
name: phpunit-file-content-tests
description: Generates PHPUnit static-analysis tests using assertFileExists + assertStringContainsString(file_get_contents(...)) patterns. Use when user says 'add test', 'write tests', 'test this file', or adds new files to tests/. Covers declare(strict_types=1), Detain\MyAdminPayum\Tests namespace, setUp() with dirname(__DIR__) path construction. Do NOT use for integration tests, live gateway calls, or tests that execute PHP scripts directly.
---
# PHPUnit File-Content Tests

## Critical

- **Never execute source scripts** — gateway/storage scripts require live credentials and DBs. Always use `file_get_contents()` + `assertStringContainsString()` for static analysis only.
- Every test file **must** have `declare(strict_types=1)` on line 3 — missing it will cause CI failures.
- Namespace is always `Detain\MyAdminPayum\Tests` — no sub-namespaces, even for sub-feature tests.
- Use `dirname(__DIR__)` in `setUp()` — never hardcode absolute paths or use `__DIR__` alone.
- One `setUp()` property per test class; point it at the most specific directory needed (`/src`, `/src/storages`, `/src/paypal_express_checkout_nvp`, etc.).

## Instructions

### Step 1 — Create the test file

Create a new test file in `tests/`. The filename must match the `FeatureNameTest.php` convention and match the class name exactly.

Boilerplate (copy verbatim, change only the class name and `$baseDir` value):

```php
<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the <feature> script files.
 *
 * Static analysis tests that verify the <feature> scripts contain
 * the expected patterns. These scripts cannot be executed directly in tests.
 */
class YourFeatureTest extends TestCase
{
    /**
     * Base path for the <feature> source directory.
     *
     * @var string
     */
    private string $baseDir;

    /**
     * Set up the base directory path.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->baseDir = dirname(__DIR__) . '/src/your_feature_dir';
    }
}
```

Verify the new test file exists before proceeding.

### Step 2 — Add file-existence tests

For each file the feature owns, add one `assertFileExists` test:

```php
public function testCapturePhpExists(): void
{
    $this->assertFileExists($this->baseDir . '/capture.php');
}
```

For checking multiple files in one method (e.g. a directory group), use `foreach`:

```php
public function testAllScriptsExist(): void
{
    $files = ['capture.php', 'config.php', 'done.php', 'prepare.php'];
    foreach ($files as $file) {
        $this->assertFileExists(
            $this->baseDir . '/' . $file,
            "your_feature/{$file} should exist"
        );
    }
}
```

Verify each expected source file actually exists at `src/your_feature_dir/` before writing the assertion.

### Step 3 — Add PHP-tag sanity tests

For each PHP file group, add a test that every file opens with `<?php`:

```php
public function testAllScriptsStartWithPhpTag(): void
{
    $files = ['capture.php', 'config.php', 'done.php', 'prepare.php'];
    foreach ($files as $file) {
        $contents = file_get_contents($this->baseDir . '/' . $file);
        $this->assertStringStartsWith('<?php', $contents, "{$file} should start with <?php");
    }
}
```

### Step 4 — Add content-assertion tests

For each meaningful pattern in a file, add one focused `assertStringContainsString` test. Each test method checks **one logical concern**:

```php
public function testConfigReferencesPayumBuilder(): void
{
    $contents = file_get_contents($this->baseDir . '/config.php');
    $this->assertStringContainsString('PayumBuilder', $contents);
}

public function testConfigIncludesCredentialFields(): void
{
    $contents = file_get_contents($this->baseDir . '/config.php');
    $this->assertStringContainsString('username', $contents);
    $this->assertStringContainsString('password', $contents);
    $this->assertStringContainsString('signature', $contents);
    $this->assertStringContainsString('sandbox', $contents);
}

public function testCaptureHandlesHttpRedirect(): void
{
    $contents = file_get_contents($this->baseDir . '/capture.php');
    $this->assertStringContainsString('HttpRedirect', $contents);
    $this->assertStringContainsString('invalidate', $contents);
}
```

Group related string checks (e.g. credential fields) into one method; keep unrelated checks in separate methods.

### Step 5 — Run the tests

```bash
vendor/bin/phpunit tests/
```

All tests must pass before committing. Fix any `assertFileExists` failures by verifying the source path, not by removing the assertion.

## Examples

**User says:** "Add tests for the recurring payments scripts."

**Actions taken:**
1. Created `tests/RecurringPaymentScriptsTest.php`
2. Set `$this->baseDir = dirname(__DIR__) . '/src/recurring_payments';` in `setUp()`
3. Added `testAllRecurringPaymentScriptsExist()` with `foreach` over `['config.php', 'prepare.php', 'create_recurring_payment.php', 'cancel_recurring_payment.php']`
4. Added `testAllScriptsStartWithPhpTag()` iterating the same list
5. Added `testConfigUsesFilesystemStorage()` checking `'FilesystemStorage'` in `config.php`
6. Added `testCancelScriptReferencesCancelRequest()` checking `'Cancel'` and `'isCanceled'` in `cancel_recurring_payment.php`
7. Added `testPrepareUsesBillingType()` checking `'BILLINGTYPE_RECURRING_PAYMENTS'` in `prepare.php`

**Result:** `tests/RecurringPaymentScriptsTest.php`
```php
<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

class RecurringPaymentScriptsTest extends TestCase
{
    private string $baseDir;

    protected function setUp(): void
    {
        $this->baseDir = dirname(__DIR__) . '/src/recurring_payments';
    }

    public function testAllRecurringPaymentScriptsExist(): void
    {
        $files = ['config.php', 'prepare.php', 'create_recurring_payment.php', 'cancel_recurring_payment.php'];
        foreach ($files as $file) {
            $this->assertFileExists(
                $this->baseDir . '/' . $file,
                "recurring_payments/{$file} should exist"
            );
        }
    }

    public function testAllScriptsStartWithPhpTag(): void
    {
        $files = ['config.php', 'prepare.php', 'create_recurring_payment.php', 'cancel_recurring_payment.php'];
        foreach ($files as $file) {
            $contents = file_get_contents($this->baseDir . '/' . $file);
            $this->assertStringStartsWith('<?php', $contents, "{$file} should start with <?php");
        }
    }

    public function testConfigUsesFilesystemStorage(): void
    {
        $contents = file_get_contents($this->baseDir . '/config.php');
        $this->assertStringContainsString('FilesystemStorage', $contents);
    }

    public function testCancelScriptReferencesCancelRequest(): void
    {
        $contents = file_get_contents($this->baseDir . '/cancel_recurring_payment.php');
        $this->assertStringContainsString('Cancel', $contents);
        $this->assertStringContainsString('isCanceled', $contents);
    }

    public function testPrepareUsesBillingType(): void
    {
        $contents = file_get_contents($this->baseDir . '/prepare.php');
        $this->assertStringContainsString('BILLINGTYPE_RECURRING_PAYMENTS', $contents);
    }
}
```

## Common Issues

**`assertFileExists` fails with "file does not exist"**
- The path built from `dirname(__DIR__) . '/src/...'` is wrong. From `tests/`, `dirname(__DIR__)` is the package root. Double-check: `ls src/your_feature_dir/`.

**`assertStringContainsString` fails**
- Print the actual file content: `var_dump(file_get_contents($this->baseDir . '/file.php'));` inside the test to see what is actually there. The string you are searching for may differ in case or whitespace.

**`declare(strict_types=1)` parse error**
- It must appear on line 3, directly after `<?php` and a blank line. Anything before it (including comments) causes a fatal parse error.

**Class not found / autoload failure**
- Ensure `"Detain\\MyAdminPayum\\Tests\\"` maps to `"tests/"` in `composer.json` `autoload-dev.psr-4`. Run `composer dump-autoload` after adding a new test class.

**Wrong namespace causes test to not be discovered**
- PHPUnit discovers tests by namespace + class name. Namespace must be exactly `Detain\MyAdminPayum\Tests` — not `Detain\MyAdminPayum\Tests\SubDir`.
