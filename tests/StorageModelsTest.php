<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the Doctrine ORM and MongoDB ODM storage model classes.
 *
 * Validates that model files exist with proper structure, annotations,
 * and inheritance from Payum base classes.
 */
class StorageModelsTest extends TestCase
{
    /**
     * Tests that the Doctrine ORM Payment model file exists.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentFileExists(): void
    {
        $this->assertFileExists(
            __DIR__ . '/../src/storages/doctrine_orm/Payment.php'
        );
    }

    /**
     * Tests that the Doctrine ORM PaymentToken model file exists.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentTokenFileExists(): void
    {
        $this->assertFileExists(
            __DIR__ . '/../src/storages/doctrine_orm/PaymentToken.php'
        );
    }

    /**
     * Tests that the MongoDB ODM Payment model file exists.
     *
     * @return void
     */
    public function testMongoOdmPaymentFileExists(): void
    {
        $this->assertFileExists(
            __DIR__ . '/../src/storages/doctrine_mongoodm/Payment.php'
        );
    }

    /**
     * Tests that the MongoDB ODM PaymentToken model file exists.
     *
     * @return void
     */
    public function testMongoOdmPaymentTokenFileExists(): void
    {
        $this->assertFileExists(
            __DIR__ . '/../src/storages/doctrine_mongoodm/PaymentToken.php'
        );
    }

    /**
     * Tests that the Doctrine ORM Payment model extends BasePayment.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentExtendsBasePayment(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/Payment.php');
        $this->assertStringContainsString('extends BasePayment', $contents);
    }

    /**
     * Tests that the Doctrine ORM Payment model uses proper namespace.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentNamespace(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/Payment.php');
        $this->assertStringContainsString('namespace Acme\Entity', $contents);
    }

    /**
     * Tests that the Doctrine ORM Payment model has ORM annotations.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentHasOrmAnnotations(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/Payment.php');
        $this->assertStringContainsString('@ORM\Entity', $contents);
        $this->assertStringContainsString('@ORM\Table', $contents);
        $this->assertStringContainsString('@ORM\Id', $contents);
        $this->assertStringContainsString('@ORM\Column', $contents);
        $this->assertStringContainsString('@ORM\GeneratedValue', $contents);
    }

    /**
     * Tests that the Doctrine ORM Payment model has a protected $id property.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentHasIdProperty(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/Payment.php');
        $this->assertStringContainsString('protected $id', $contents);
    }

    /**
     * Tests that the Doctrine ORM PaymentToken extends Token.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentTokenExtendsToken(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/PaymentToken.php');
        $this->assertStringContainsString('extends Token', $contents);
    }

    /**
     * Tests that the Doctrine ORM PaymentToken uses proper namespace.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentTokenNamespace(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/PaymentToken.php');
        $this->assertStringContainsString('namespace Acme\Entity', $contents);
    }

    /**
     * Tests that the MongoDB ODM Payment model extends BasePayment.
     *
     * @return void
     */
    public function testMongoOdmPaymentExtendsBasePayment(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/Payment.php');
        $this->assertStringContainsString('extends BasePayment', $contents);
    }

    /**
     * Tests that the MongoDB ODM Payment model uses proper namespace.
     *
     * @return void
     */
    public function testMongoOdmPaymentNamespace(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/Payment.php');
        $this->assertStringContainsString('namespace Acme\Document', $contents);
    }

    /**
     * Tests that the MongoDB ODM Payment model has Mongo annotations.
     *
     * @return void
     */
    public function testMongoOdmPaymentHasMongoAnnotations(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/Payment.php');
        $this->assertStringContainsString('@Mongo\Document', $contents);
        $this->assertStringContainsString('@Mongo\Id', $contents);
    }

    /**
     * Tests that the MongoDB ODM Payment model has a protected $id property.
     *
     * @return void
     */
    public function testMongoOdmPaymentHasIdProperty(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/Payment.php');
        $this->assertStringContainsString('protected $id', $contents);
    }

    /**
     * Tests that the MongoDB ODM PaymentToken extends Token.
     *
     * @return void
     */
    public function testMongoOdmPaymentTokenExtendsToken(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/PaymentToken.php');
        $this->assertStringContainsString('extends Token', $contents);
    }

    /**
     * Tests that the MongoDB ODM PaymentToken uses proper namespace.
     *
     * @return void
     */
    public function testMongoOdmPaymentTokenNamespace(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/PaymentToken.php');
        $this->assertStringContainsString('namespace Acme\Document', $contents);
    }

    /**
     * Tests that the MongoDB ODM PaymentToken has Mongo Document annotation.
     *
     * @return void
     */
    public function testMongoOdmPaymentTokenHasMongoAnnotation(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/PaymentToken.php');
        $this->assertStringContainsString('@Mongo\Document', $contents);
    }

    /**
     * Tests that Doctrine ORM Payment model imports Payum base Payment.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentImportsBasePayment(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/Payment.php');
        $this->assertStringContainsString('use Payum\Core\Model\Payment as BasePayment', $contents);
    }

    /**
     * Tests that Doctrine ORM PaymentToken imports Payum Token.
     *
     * @return void
     */
    public function testDoctrineOrmPaymentTokenImportsToken(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_orm/PaymentToken.php');
        $this->assertStringContainsString('use Payum\Core\Model\Token', $contents);
    }

    /**
     * Tests that MongoDB ODM Payment model imports Payum base Payment.
     *
     * @return void
     */
    public function testMongoOdmPaymentImportsBasePayment(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/Payment.php');
        $this->assertStringContainsString('use Payum\Core\Model\Payment as BasePayment', $contents);
    }

    /**
     * Tests that MongoDB ODM PaymentToken imports Payum Token.
     *
     * @return void
     */
    public function testMongoOdmPaymentTokenImportsToken(): void
    {
        $contents = file_get_contents(__DIR__ . '/../src/storages/doctrine_mongoodm/PaymentToken.php');
        $this->assertStringContainsString('use Payum\Core\Model\Token', $contents);
    }
}
