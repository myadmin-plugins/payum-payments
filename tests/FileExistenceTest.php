<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the existence and basic structure of all source files.
 *
 * Ensures that every expected file in the package is present
 * and contains valid PHP where applicable.
 */
class FileExistenceTest extends TestCase
{
    /**
     * Base path for the package source directory.
     *
     * @var string
     */
    private string $srcDir;

    /**
     * Set up the base source directory path.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->srcDir = dirname(__DIR__) . '/src';
    }

    /**
     * Tests that the Plugin.php file exists.
     *
     * @return void
     */
    public function testPluginPhpExists(): void
    {
        $this->assertFileExists($this->srcDir . '/Plugin.php');
    }

    /**
     * Tests that the currency_details.php file exists.
     *
     * @return void
     */
    public function testCurrencyDetailsExists(): void
    {
        $this->assertFileExists($this->srcDir . '/currency_details.php');
    }

    /**
     * Tests that the paypal_ipn.php file exists.
     *
     * @return void
     */
    public function testPaypalIpnExists(): void
    {
        $this->assertFileExists($this->srcDir . '/paypal_ipn.php');
    }

    /**
     * Tests that the sensitive_info.php file exists.
     *
     * @return void
     */
    public function testSensitiveInfoExists(): void
    {
        $this->assertFileExists($this->srcDir . '/sensitive_info.php');
    }

    /**
     * Tests that the logger directory files exist.
     *
     * @return void
     */
    public function testLoggerDirectoryFilesExist(): void
    {
        $this->assertFileExists($this->srcDir . '/logger/LoggerAwareAction.php');
        $this->assertFileExists($this->srcDir . '/logger/debugging.php');
        $this->assertFileExists($this->srcDir . '/logger/logger_Extension_when_new_gateway.php');
    }

    /**
     * Tests that the getting_started directory files exist.
     *
     * @return void
     */
    public function testGettingStartedDirectoryFilesExist(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            $pattern = $this->srcDir . '/getting_started/' . $i . '_*.php';
            $files = glob($pattern);
            $this->assertNotEmpty($files, "Expected getting_started file {$i}_*.php to exist");
        }
    }

    /**
     * Tests that the paypal_express_checkout_nvp directory files exist.
     *
     * @return void
     */
    public function testPaypalExpressCheckoutNvpFilesExist(): void
    {
        $files = ['capture.php', 'config.php', 'done.php', 'prepare.php'];
        foreach ($files as $file) {
            $this->assertFileExists(
                $this->srcDir . '/paypal_express_checkout_nvp/' . $file,
                "paypal_express_checkout_nvp/{$file} should exist"
            );
        }
    }

    /**
     * Tests that the recurring_payments directory files exist.
     *
     * @return void
     */
    public function testRecurringPaymentsFilesExist(): void
    {
        $files = [
            'AgreementDetails.php',
            'RecurringPaymentDetais.php',
            'cancel_recurring_payment.php',
            'config.php',
            'create_recurring_payment.php',
            'prepare.php',
        ];
        foreach ($files as $file) {
            $this->assertFileExists(
                $this->srcDir . '/recurring_payments/' . $file,
                "recurring_payments/{$file} should exist"
            );
        }
    }

    /**
     * Tests that the storages directory files exist.
     *
     * @return void
     */
    public function testStoragesDirectoryFilesExist(): void
    {
        $files = [
            'doctrine_orm/Payment.php',
            'doctrine_orm/PaymentToken.php',
            'doctrine_orm/entity_manager_and_payum_storage.php',
            'doctrine_mongoodm/Payment.php',
            'doctrine_mongoodm/PaymentToken.php',
            'doctrine_mongoodm/entity_manager_and_payum_storage.php',
            'filesystem/entity_manager_and_payum_storage.php',
            'propel2/connection.php',
            'propel2/custom_storage.php',
            'explicityly_used.php',
            'implicitly_used.php',
            'model-identity_with_extension.php',
        ];
        foreach ($files as $file) {
            $this->assertFileExists(
                $this->srcDir . '/storages/' . $file,
                "storages/{$file} should exist"
            );
        }
    }

    /**
     * Tests that the composer.json file exists at the package root.
     *
     * @return void
     */
    public function testComposerJsonExists(): void
    {
        $this->assertFileExists(dirname(__DIR__) . '/composer.json');
    }

    /**
     * Tests that the Plugin.php file contains valid PHP opening tag.
     *
     * @return void
     */
    public function testPluginFileStartsWithPhpTag(): void
    {
        $contents = file_get_contents($this->srcDir . '/Plugin.php');
        $this->assertStringStartsWith('<?php', $contents);
    }

    /**
     * Tests that the authorize_token_custom_query_parameter.txt file exists.
     *
     * @return void
     */
    public function testAuthorizeTokenCustomQueryParameterExists(): void
    {
        $this->assertFileExists($this->srcDir . '/authorize_token_custom_query_parameter.txt');
    }
}
