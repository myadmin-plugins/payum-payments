<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the recurring payment script files.
 *
 * Static analysis tests that verify the recurring payment scripts contain
 * the expected Payum request classes, PayPal API references, and proper
 * payment flow structure.
 */
class RecurringPaymentScriptsTest extends TestCase
{
    /**
     * Base path for the recurring payments source directory.
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
        $this->baseDir = dirname(__DIR__) . '/src/recurring_payments';
    }

    /**
     * Tests that the config script sets up FilesystemStorage for agreement and recurring payment classes.
     *
     * @return void
     */
    public function testConfigSetsUpFilesystemStorage(): void
    {
        $contents = file_get_contents($this->baseDir . '/config.php');
        $this->assertStringContainsString('FilesystemStorage', $contents);
        $this->assertStringContainsString('AgreementDetails', $contents);
        $this->assertStringContainsString('RecurringPaymentDetails', $contents);
    }

    /**
     * Tests that the config script creates storages array entries.
     *
     * @return void
     */
    public function testConfigCreatesStoragesArray(): void
    {
        $contents = file_get_contents($this->baseDir . '/config.php');
        $this->assertStringContainsString('$storages[', $contents);
    }

    /**
     * Tests that the cancel recurring payment script uses Cancel and Sync requests.
     *
     * @return void
     */
    public function testCancelScriptUsesCancelAndSyncRequests(): void
    {
        $contents = file_get_contents($this->baseDir . '/cancel_recurring_payment.php');
        $this->assertStringContainsString('Cancel', $contents);
        $this->assertStringContainsString('Sync', $contents);
    }

    /**
     * Tests that the cancel recurring payment script checks cancellation status.
     *
     * @return void
     */
    public function testCancelScriptChecksCancellationStatus(): void
    {
        $contents = file_get_contents($this->baseDir . '/cancel_recurring_payment.php');
        $this->assertStringContainsString('GetHumanStatus', $contents);
        $this->assertStringContainsString('isCanceled', $contents);
    }

    /**
     * Tests that the create recurring payment script uses CreateRecurringPaymentProfile.
     *
     * @return void
     */
    public function testCreateScriptUsesCreateRecurringPaymentProfile(): void
    {
        $contents = file_get_contents($this->baseDir . '/create_recurring_payment.php');
        $this->assertStringContainsString('CreateRecurringPaymentProfile', $contents);
    }

    /**
     * Tests that the create recurring payment script sets expected payment fields.
     *
     * @return void
     */
    public function testCreateScriptSetsExpectedPaymentFields(): void
    {
        $contents = file_get_contents($this->baseDir . '/create_recurring_payment.php');
        $this->assertStringContainsString("'TOKEN'", $contents);
        $this->assertStringContainsString("'DESC'", $contents);
        $this->assertStringContainsString("'EMAIL'", $contents);
        $this->assertStringContainsString("'AMT'", $contents);
        $this->assertStringContainsString("'CURRENCYCODE'", $contents);
        $this->assertStringContainsString("'BILLINGFREQUENCY'", $contents);
        $this->assertStringContainsString("'PROFILESTARTDATE'", $contents);
        $this->assertStringContainsString("'BILLINGPERIOD'", $contents);
    }

    /**
     * Tests that the create recurring payment script verifies agreement status.
     *
     * @return void
     */
    public function testCreateScriptVerifiesAgreementStatus(): void
    {
        $contents = file_get_contents($this->baseDir . '/create_recurring_payment.php');
        $this->assertStringContainsString('isCaptured', $contents);
        $this->assertStringContainsString('400 Bad Request', $contents);
    }

    /**
     * Tests that the prepare script uses PayPal billing type constants.
     *
     * @return void
     */
    public function testPrepareScriptUsesBillingType(): void
    {
        $contents = file_get_contents($this->baseDir . '/prepare.php');
        $this->assertStringContainsString('BILLINGTYPE_RECURRING_PAYMENTS', $contents);
        $this->assertStringContainsString('L_BILLINGAGREEMENTDESCRIPTION0', $contents);
    }

    /**
     * Tests that the prepare script creates a capture token.
     *
     * @return void
     */
    public function testPrepareScriptCreatesCaptureToken(): void
    {
        $contents = file_get_contents($this->baseDir . '/prepare.php');
        $this->assertStringContainsString('createCaptureToken', $contents);
    }

    /**
     * Tests that all recurring payment scripts start with PHP tag.
     *
     * @return void
     */
    public function testAllScriptsStartWithPhpTag(): void
    {
        $files = [
            'cancel_recurring_payment.php',
            'config.php',
            'create_recurring_payment.php',
            'prepare.php',
        ];
        foreach ($files as $file) {
            $contents = file_get_contents($this->baseDir . '/' . $file);
            $this->assertStringStartsWith('<?php', $contents, "{$file} should start with <?php");
        }
    }
}
