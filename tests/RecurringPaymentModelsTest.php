<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the recurring payment model classes.
 *
 * Validates the AgreementDetails and RecurringPaymentDetails classes
 * that extend ArrayObject for Payum recurring payment handling.
 */
class RecurringPaymentModelsTest extends TestCase
{
    /**
     * Tests that the AgreementDetails source file exists.
     *
     * @return void
     */
    public function testAgreementDetailsFileExists(): void
    {
        $this->assertFileExists(
            __DIR__ . '/../src/recurring_payments/AgreementDetails.php'
        );
    }

    /**
     * Tests that the RecurringPaymentDetails source file exists.
     *
     * @return void
     */
    public function testRecurringPaymentDetailsFileExists(): void
    {
        $this->assertFileExists(
            __DIR__ . '/../src/recurring_payments/RecurringPaymentDetais.php'
        );
    }

    /**
     * Tests that AgreementDetails extends ArrayObject.
     *
     * @return void
     */
    public function testAgreementDetailsExtendsArrayObject(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/recurring_payments/AgreementDetails.php');
        $this->assertStringContainsString('extends \ArrayObject', $contents);
    }

    /**
     * Tests that RecurringPaymentDetails extends ArrayObject.
     *
     * @return void
     */
    public function testRecurringPaymentDetailsExtendsArrayObject(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/recurring_payments/RecurringPaymentDetais.php');
        $this->assertStringContainsString('extends \ArrayObject', $contents);
    }

    /**
     * Tests that AgreementDetails has the correct namespace.
     *
     * @return void
     */
    public function testAgreementDetailsNamespace(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/recurring_payments/AgreementDetails.php');
        $this->assertStringContainsString('namespace App\Model', $contents);
    }

    /**
     * Tests that RecurringPaymentDetails has the correct namespace.
     *
     * @return void
     */
    public function testRecurringPaymentDetailsNamespace(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/recurring_payments/RecurringPaymentDetais.php');
        $this->assertStringContainsString('namespace App\Model', $contents);
    }

    /**
     * Tests that AgreementDetails has proper docblock.
     *
     * @return void
     */
    public function testAgreementDetailsHasDocblock(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/recurring_payments/AgreementDetails.php');
        $this->assertStringContainsString('Class AgreementDetails', $contents);
    }

    /**
     * Tests that RecurringPaymentDetails has proper docblock.
     *
     * @return void
     */
    public function testRecurringPaymentDetailsHasDocblock(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/recurring_payments/RecurringPaymentDetais.php');
        $this->assertStringContainsString('Class RecurringPaymentDetails', $contents);
    }
}
