<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the storage script files.
 *
 * Static analysis tests that verify the storage example scripts contain
 * the expected Payum storage patterns, class references, and configurations.
 * These scripts interact with databases/filesystems and cannot be executed
 * directly in tests, so we validate their structure instead.
 */
class StorageScriptsTest extends TestCase
{
    /**
     * Base path for the storages source directory.
     *
     * @var string
     */
    private string $storagesDir;

    /**
     * Set up the base storages directory path.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->storagesDir = dirname(__DIR__) . '/src/storages';
    }

    /**
     * Tests that the explicitly used storage script references FilesystemStorage.
     *
     * @return void
     */
    public function testExplicitlyUsedReferencesFilesystemStorage(): void
    {
        $contents = file_get_contents($this->storagesDir . '/explicityly_used.php');
        $this->assertStringContainsString('FilesystemStorage', $contents);
    }

    /**
     * Tests that the explicitly used storage script calls storage create and update.
     *
     * @return void
     */
    public function testExplicitlyUsedCallsCreateAndUpdate(): void
    {
        $contents = file_get_contents($this->storagesDir . '/explicityly_used.php');
        $this->assertStringContainsString('$storage->create()', $contents);
        $this->assertStringContainsString('$storage->update(', $contents);
    }

    /**
     * Tests that the implicitly used storage script references StorageExtension.
     *
     * @return void
     */
    public function testImplicitlyUsedReferencesStorageExtension(): void
    {
        $contents = file_get_contents($this->storagesDir . '/implicitly_used.php');
        $this->assertStringContainsString('StorageExtension', $contents);
    }

    /**
     * Tests that the model-identity script references both Identity and Capture.
     *
     * @return void
     */
    public function testModelIdentityReferencesExpectedClasses(): void
    {
        $contents = file_get_contents($this->storagesDir . '/model-identity_with_extension.php');
        $this->assertStringContainsString('StorageExtension', $contents);
        $this->assertStringContainsString('Capture', $contents);
        $this->assertStringContainsString('Identity', $contents);
    }

    /**
     * Tests that the Doctrine ORM entity manager script references DoctrineStorage.
     *
     * @return void
     */
    public function testDoctrineOrmEntityManagerReferencesDoctrineStorage(): void
    {
        $contents = file_get_contents($this->storagesDir . '/doctrine_orm/entity_manager_and_payum_storage.php');
        $this->assertStringContainsString('DoctrineStorage', $contents);
    }

    /**
     * Tests that the Doctrine ORM entity manager script configures SQLite.
     *
     * @return void
     */
    public function testDoctrineOrmEntityManagerConfiguresSqlite(): void
    {
        $contents = file_get_contents($this->storagesDir . '/doctrine_orm/entity_manager_and_payum_storage.php');
        $this->assertStringContainsString('pdo_sqlite', $contents);
    }

    /**
     * Tests that the Doctrine ORM entity manager script references EntityManager.
     *
     * @return void
     */
    public function testDoctrineOrmEntityManagerReferencesEntityManager(): void
    {
        $contents = file_get_contents($this->storagesDir . '/doctrine_orm/entity_manager_and_payum_storage.php');
        $this->assertStringContainsString('EntityManager', $contents);
    }

    /**
     * Tests that the MongoDB ODM entity manager script references DocumentManager.
     *
     * @return void
     */
    public function testMongoOdmEntityManagerReferencesDocumentManager(): void
    {
        $contents = file_get_contents($this->storagesDir . '/doctrine_mongoodm/entity_manager_and_payum_storage.php');
        $this->assertStringContainsString('DocumentManager', $contents);
    }

    /**
     * Tests that the MongoDB ODM entity manager script configures a test database.
     *
     * @return void
     */
    public function testMongoOdmEntityManagerConfiguresTestDb(): void
    {
        $contents = file_get_contents($this->storagesDir . '/doctrine_mongoodm/entity_manager_and_payum_storage.php');
        $this->assertStringContainsString('payum_tests', $contents);
    }

    /**
     * Tests that the filesystem storage script references FilesystemStorage.
     *
     * @return void
     */
    public function testFilesystemStorageReferencesFilesystemStorage(): void
    {
        $contents = file_get_contents($this->storagesDir . '/filesystem/entity_manager_and_payum_storage.php');
        $this->assertStringContainsString('FilesystemStorage', $contents);
    }

    /**
     * Tests that the Propel2 connection script references ConnectionManagerSingle.
     *
     * @return void
     */
    public function testPropel2ConnectionReferencesConnectionManager(): void
    {
        $contents = file_get_contents($this->storagesDir . '/propel2/connection.php');
        $this->assertStringContainsString('ConnectionManagerSingle', $contents);
    }

    /**
     * Tests that the Propel2 custom storage script implements StorageInterface.
     *
     * @return void
     */
    public function testPropel2CustomStorageImplementsStorageInterface(): void
    {
        $contents = file_get_contents($this->storagesDir . '/propel2/custom_storage.php');
        $this->assertStringContainsString('StorageInterface', $contents);
        $this->assertStringContainsString('implements StorageInterface', $contents);
    }

    /**
     * Tests that all storage PHP files start with a PHP opening tag.
     *
     * @return void
     */
    public function testAllStoragePhpFilesHavePhpOpeningTag(): void
    {
        $files = [
            'explicityly_used.php',
            'implicitly_used.php',
            'model-identity_with_extension.php',
            'doctrine_orm/entity_manager_and_payum_storage.php',
            'doctrine_mongoodm/entity_manager_and_payum_storage.php',
            'filesystem/entity_manager_and_payum_storage.php',
            'propel2/connection.php',
            'propel2/custom_storage.php',
        ];
        foreach ($files as $file) {
            $contents = file_get_contents($this->storagesDir . '/' . $file);
            $this->assertStringStartsWith('<?php', $contents, "{$file} should start with <?php");
        }
    }
}
