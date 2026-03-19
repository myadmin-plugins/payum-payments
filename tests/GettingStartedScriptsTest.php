<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the getting started example script files.
 *
 * Static analysis tests that verify the getting started scripts contain
 * the expected Payum patterns for PayPal and Stripe integrations.
 */
class GettingStartedScriptsTest extends TestCase
{
    /**
     * Base path for the getting_started source directory.
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
        $this->baseDir = dirname(__DIR__) . '/src/getting_started';
    }

    /**
     * Tests that the PayPal create gateway script references the factory.
     *
     * @return void
     */
    public function testPaypalCreateGatewayReferencesFactory(): void
    {
        $contents = file_get_contents($this->baseDir . '/1_paypal_create_gateway.php');
        $this->assertStringContainsString('PaypalExpressCheckoutGatewayFactory', $contents);
    }

    /**
     * Tests that the PayPal create gateway script includes credential configuration.
     *
     * @return void
     */
    public function testPaypalCreateGatewayIncludesCredentials(): void
    {
        $contents = file_get_contents($this->baseDir . '/1_paypal_create_gateway.php');
        $this->assertStringContainsString('username', $contents);
        $this->assertStringContainsString('password', $contents);
        $this->assertStringContainsString('signature', $contents);
    }

    /**
     * Tests that the PayPal redirects script references Capture request.
     *
     * @return void
     */
    public function testPaypalRedirectsReferencesCapture(): void
    {
        $contents = file_get_contents($this->baseDir . '/2_paypal_redirects.php');
        $this->assertStringContainsString('Capture', $contents);
    }

    /**
     * Tests that the PayPal redirects script sets RETURNURL and CANCELURL.
     *
     * @return void
     */
    public function testPaypalRedirectsSetsUrls(): void
    {
        $contents = file_get_contents($this->baseDir . '/2_paypal_redirects.php');
        $this->assertStringContainsString('RETURNURL', $contents);
        $this->assertStringContainsString('CANCELURL', $contents);
    }

    /**
     * Tests that the PayPal handle redirect script catches ReplyInterface.
     *
     * @return void
     */
    public function testPaypalHandleRedirectCatchesReply(): void
    {
        $contents = file_get_contents($this->baseDir . '/3_paypal_handle_redirect.php');
        $this->assertStringContainsString('ReplyInterface', $contents);
        $this->assertStringContainsString('HttpRedirect', $contents);
    }

    /**
     * Tests that the get gateway script references GetHumanStatus.
     *
     * @return void
     */
    public function testGetGatewayReferencesGetHumanStatus(): void
    {
        $contents = file_get_contents($this->baseDir . '/4_get_gateway.php');
        $this->assertStringContainsString('GetHumanStatus', $contents);
    }

    /**
     * Tests that the get gateway script checks multiple status methods.
     *
     * @return void
     */
    public function testGetGatewayChecksMultipleStatuses(): void
    {
        $contents = file_get_contents($this->baseDir . '/4_get_gateway.php');
        $this->assertStringContainsString('isNew()', $contents);
        $this->assertStringContainsString('isCaptured()', $contents);
        $this->assertStringContainsString('isCanceled()', $contents);
        $this->assertStringContainsString('isFailed()', $contents);
    }

    /**
     * Tests that the Stripe JS create gateway script references the factory.
     *
     * @return void
     */
    public function testStripeJsCreateGatewayReferencesFactory(): void
    {
        $contents = file_get_contents($this->baseDir . '/5_stripe_js_create_gateway.php');
        $this->assertStringContainsString('StripeJsGatewayFactory', $contents);
    }

    /**
     * Tests that the Stripe JS create gateway script includes key configuration.
     *
     * @return void
     */
    public function testStripeJsCreateGatewayIncludesKeys(): void
    {
        $contents = file_get_contents($this->baseDir . '/5_stripe_js_create_gateway.php');
        $this->assertStringContainsString('publishable_key', $contents);
        $this->assertStringContainsString('secret_key', $contents);
    }

    /**
     * Tests that the Stripe JS capture script references Capture request.
     *
     * @return void
     */
    public function testStripeJsCaptureReferencesCapture(): void
    {
        $contents = file_get_contents($this->baseDir . '/6_stripe_js_capture.php');
        $this->assertStringContainsString('Capture', $contents);
    }

    /**
     * Tests that the Stripe JS render form script catches HttpResponse.
     *
     * @return void
     */
    public function testStripeJsRenderFormCatchesHttpResponse(): void
    {
        $contents = file_get_contents($this->baseDir . '/7_stripe_js_render_form.php');
        $this->assertStringContainsString('HttpResponse', $contents);
    }

    /**
     * Tests that the Stripe JS credit card script references CreditCard model.
     *
     * @return void
     */
    public function testStripeJsCreditCardReferencesCreditCardModel(): void
    {
        $contents = file_get_contents($this->baseDir . '/8_stripe_js_credit_card.php');
        $this->assertStringContainsString('CreditCard', $contents);
        $this->assertStringContainsString('setNumber', $contents);
        $this->assertStringContainsString('setExpireAt', $contents);
        $this->assertStringContainsString('setSecurityCode', $contents);
    }

    /**
     * Tests that all getting started scripts start with PHP tag.
     *
     * @return void
     */
    public function testAllScriptsStartWithPhpTag(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            $pattern = $this->baseDir . '/' . $i . '_*.php';
            $files = glob($pattern);
            $this->assertNotEmpty($files, "Expected file {$i}_*.php to exist");
            foreach ($files as $file) {
                $contents = file_get_contents($file);
                $this->assertStringStartsWith('<?php', $contents, basename($file) . ' should start with <?php');
            }
        }
    }
}
