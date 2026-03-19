<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the PayPal Express Checkout NVP script files.
 *
 * Static analysis tests that verify the PayPal integration scripts contain
 * the expected Payum request classes, configuration patterns, and gateway
 * setup code. These scripts interact with external APIs and cannot be
 * executed directly in tests.
 */
class PaypalExpressCheckoutScriptsTest extends TestCase
{
    /**
     * Base path for the PayPal Express Checkout NVP source directory.
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
        $this->baseDir = dirname(__DIR__) . '/src/paypal_express_checkout_nvp';
    }

    /**
     * Tests that the config script references PayumBuilder.
     *
     * @return void
     */
    public function testConfigReferencesPayumBuilder(): void
    {
        $contents = file_get_contents($this->baseDir . '/config.php');
        $this->assertStringContainsString('PayumBuilder', $contents);
    }

    /**
     * Tests that the config script sets up paypal_express_checkout factory.
     *
     * @return void
     */
    public function testConfigUsesPaypalExpressCheckoutFactory(): void
    {
        $contents = file_get_contents($this->baseDir . '/config.php');
        $this->assertStringContainsString('paypal_express_checkout', $contents);
    }

    /**
     * Tests that the config script includes required PayPal credentials.
     *
     * @return void
     */
    public function testConfigIncludesCredentialFields(): void
    {
        $contents = file_get_contents($this->baseDir . '/config.php');
        $this->assertStringContainsString('username', $contents);
        $this->assertStringContainsString('password', $contents);
        $this->assertStringContainsString('signature', $contents);
        $this->assertStringContainsString('sandbox', $contents);
    }

    /**
     * Tests that the capture script references the Capture request class pattern.
     *
     * @return void
     */
    public function testCaptureReferencesCapture(): void
    {
        $contents = file_get_contents($this->baseDir . '/capture.php');
        $this->assertStringContainsString('Capture', $contents);
    }

    /**
     * Tests that the capture script handles HttpRedirect replies.
     *
     * @return void
     */
    public function testCaptureHandlesHttpRedirect(): void
    {
        $contents = file_get_contents($this->baseDir . '/capture.php');
        $this->assertStringContainsString('HttpRedirect', $contents);
    }

    /**
     * Tests that the capture script invalidates the token after use.
     *
     * @return void
     */
    public function testCaptureInvalidatesToken(): void
    {
        $contents = file_get_contents($this->baseDir . '/capture.php');
        $this->assertStringContainsString('invalidate', $contents);
    }

    /**
     * Tests that the done script references GetHumanStatus.
     *
     * @return void
     */
    public function testDoneReferencesGetHumanStatus(): void
    {
        $contents = file_get_contents($this->baseDir . '/done.php');
        $this->assertStringContainsString('GetHumanStatus', $contents);
    }

    /**
     * Tests that the done script outputs JSON response.
     *
     * @return void
     */
    public function testDoneOutputsJsonResponse(): void
    {
        $contents = file_get_contents($this->baseDir . '/done.php');
        $this->assertStringContainsString('json_encode', $contents);
        $this->assertStringContainsString('application/json', $contents);
    }

    /**
     * Tests that the done script includes payment details in output.
     *
     * @return void
     */
    public function testDoneIncludesPaymentDetails(): void
    {
        $contents = file_get_contents($this->baseDir . '/done.php');
        $this->assertStringContainsString('total_amount', $contents);
        $this->assertStringContainsString('currency_code', $contents);
        $this->assertStringContainsString('status', $contents);
    }

    /**
     * Tests that the prepare script creates a payment with expected fields.
     *
     * @return void
     */
    public function testPrepareCreatesPaymentWithExpectedFields(): void
    {
        $contents = file_get_contents($this->baseDir . '/prepare.php');
        $this->assertStringContainsString('setNumber', $contents);
        $this->assertStringContainsString('setCurrencyCode', $contents);
        $this->assertStringContainsString('setTotalAmount', $contents);
        $this->assertStringContainsString('setDescription', $contents);
        $this->assertStringContainsString('setClientId', $contents);
        $this->assertStringContainsString('setClientEmail', $contents);
    }

    /**
     * Tests that the prepare script creates a capture token.
     *
     * @return void
     */
    public function testPrepareCreatesCaptureToken(): void
    {
        $contents = file_get_contents($this->baseDir . '/prepare.php');
        $this->assertStringContainsString('createCaptureToken', $contents);
    }

    /**
     * Tests that all PayPal Express Checkout scripts start with PHP tag.
     *
     * @return void
     */
    public function testAllScriptsStartWithPhpTag(): void
    {
        $files = ['capture.php', 'config.php', 'done.php', 'prepare.php'];
        foreach ($files as $file) {
            $contents = file_get_contents($this->baseDir . '/' . $file);
            $this->assertStringStartsWith('<?php', $contents, "{$file} should start with <?php");
        }
    }
}
