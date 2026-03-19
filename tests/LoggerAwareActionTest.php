<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Tests for the LoggerAwareAction class.
 *
 * Validates class structure, interface implementation, and behavior
 * of the logger-aware action pattern used in Payum integrations.
 *
 * Note: The LoggerAwareAction class lives under the App\Payum\Action namespace
 * and is loaded directly from the src/logger directory.
 */
class LoggerAwareActionTest extends TestCase
{
    /**
     * Path to the LoggerAwareAction source file.
     */
    private const SOURCE_FILE = __DIR__ . '/../src/logger/LoggerAwareAction.php';

    /**
     * Tests that the LoggerAwareAction source file exists.
     *
     * @return void
     */
    public function testSourceFileExists(): void
    {
        $this->assertFileExists(self::SOURCE_FILE);
    }

    /**
     * Tests that the source file contains the expected class declaration.
     *
     * @return void
     */
    public function testSourceFileContainsClassDeclaration(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('class LoggerAwareAction', $contents);
    }

    /**
     * Tests that the source file declares it implements ActionInterface.
     *
     * @return void
     */
    public function testSourceFileDeclaresActionInterface(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('ActionInterface', $contents);
    }

    /**
     * Tests that the source file declares it implements LoggerAwareInterface.
     *
     * @return void
     */
    public function testSourceFileDeclaresLoggerAwareInterface(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('LoggerAwareInterface', $contents);
    }

    /**
     * Tests that the source file has a setLogger method.
     *
     * @return void
     */
    public function testSourceFileHasSetLoggerMethod(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('function setLogger', $contents);
    }

    /**
     * Tests that the source file has an execute method.
     *
     * @return void
     */
    public function testSourceFileHasExecuteMethod(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('function execute', $contents);
    }

    /**
     * Tests that the source file has a supports method.
     *
     * @return void
     */
    public function testSourceFileHasSupportsMethod(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('function supports', $contents);
    }

    /**
     * Tests that the source file uses the correct namespace.
     *
     * @return void
     */
    public function testSourceFileHasCorrectNamespace(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('namespace App\Payum\Action', $contents);
    }

    /**
     * Tests that the logger property is declared as protected.
     *
     * @return void
     */
    public function testLoggerPropertyIsProtected(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('protected $logger', $contents);
    }

    /**
     * Tests that the supports method checks for a specific string value.
     *
     * @return void
     */
    public function testSupportsMethodChecksForARequest(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString("'a request'", $contents);
    }

    /**
     * Tests that the execute method checks if logger is set before logging.
     *
     * @return void
     */
    public function testExecuteMethodChecksLoggerBeforeLogging(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('if ($this->logger)', $contents);
    }

    /**
     * Tests that the source file uses the Psr\Log\LoggerInterface import.
     *
     * @return void
     */
    public function testSourceFileImportsLoggerInterface(): void
    {
        $contents = file_get_contents(self::SOURCE_FILE);
        $this->assertStringContainsString('use Psr\Log\LoggerInterface', $contents);
    }
}
