<?php

declare(strict_types=1);

namespace Detain\MyAdminPayum\Tests;

use Detain\MyAdminPayum\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Tests for the Plugin class.
 *
 * Validates class structure, static properties, hook registration,
 * and event handler method signatures.
 */
class PluginTest extends TestCase
{
    /**
     * @var ReflectionClass<Plugin>
     */
    private ReflectionClass $reflection;

    /**
     * Set up the reflection instance before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Plugin::class);
    }

    /**
     * Tests that the Plugin class can be instantiated.
     *
     * @return void
     */
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Tests that the Plugin class exists in the correct namespace.
     *
     * @return void
     */
    public function testClassExistsInCorrectNamespace(): void
    {
        $this->assertSame('Detain\MyAdminPayum', $this->reflection->getNamespaceName());
    }

    /**
     * Tests that the $name static property is defined and is a string.
     *
     * @return void
     */
    public function testNamePropertyIsStaticString(): void
    {
        $prop = $this->reflection->getProperty('name');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertIsString(Plugin::$name);
        $this->assertNotEmpty(Plugin::$name);
    }

    /**
     * Tests that the $description static property is defined and is a string.
     *
     * @return void
     */
    public function testDescriptionPropertyIsStaticString(): void
    {
        $prop = $this->reflection->getProperty('description');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertIsString(Plugin::$description);
        $this->assertNotEmpty(Plugin::$description);
    }

    /**
     * Tests that the $help static property is defined and is a string.
     *
     * @return void
     */
    public function testHelpPropertyIsStaticString(): void
    {
        $prop = $this->reflection->getProperty('help');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertIsString(Plugin::$help);
    }

    /**
     * Tests that the $type static property is defined and equals 'plugin'.
     *
     * @return void
     */
    public function testTypePropertyIsPlugin(): void
    {
        $prop = $this->reflection->getProperty('type');
        $this->assertTrue($prop->isStatic());
        $this->assertTrue($prop->isPublic());
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Tests that getHooks returns an array.
     *
     * @return void
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
    }

    /**
     * Tests that getHooks is a static method.
     *
     * @return void
     */
    public function testGetHooksIsStatic(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getHooks returns array values that are valid callables when present.
     *
     * Each hook entry should be a two-element array [className, methodName].
     *
     * @return void
     */
    public function testGetHooksValuesAreValidCallableFormat(): void
    {
        $hooks = Plugin::getHooks();
        // Currently all hooks are commented out, so the array is empty
        $this->assertIsArray($hooks);
        foreach ($hooks as $eventName => $callback) {
            $this->assertIsString($eventName, 'Hook event name must be a string');
            $this->assertIsArray($callback, 'Hook callback must be an array');
            $this->assertCount(2, $callback, 'Hook callback must have exactly 2 elements');
            $this->assertSame(Plugin::class, $callback[0], 'Hook callback class must be Plugin');
            $this->assertTrue(
                $this->reflection->hasMethod($callback[1]),
                "Hook callback method '{$callback[1]}' must exist on Plugin class"
            );
        }
    }

    /**
     * Tests that the getMenu method exists and is static.
     *
     * @return void
     */
    public function testGetMenuMethodIsStatic(): void
    {
        $method = $this->reflection->getMethod('getMenu');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getMenu accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetMenuAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getMenu');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that the getRequirements method exists and is static.
     *
     * @return void
     */
    public function testGetRequirementsMethodIsStatic(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getRequirements accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetRequirementsAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $params = $method->getParameters();
        $this->assertCount(1, $params);

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that the getSettings method exists and is static.
     *
     * @return void
     */
    public function testGetSettingsMethodIsStatic(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $this->assertTrue($method->isStatic());
        $this->assertTrue($method->isPublic());
    }

    /**
     * Tests that getSettings accepts a GenericEvent parameter.
     *
     * @return void
     */
    public function testGetSettingsAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $params = $method->getParameters();
        $this->assertCount(1, $params);

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that the constructor takes no required parameters.
     *
     * @return void
     */
    public function testConstructorHasNoRequiredParameters(): void
    {
        $constructor = $this->reflection->getConstructor();
        $this->assertNotNull($constructor);
        $params = $constructor->getParameters();
        $requiredParams = array_filter($params, fn($p) => !$p->isOptional());
        $this->assertCount(0, $requiredParams);
    }

    /**
     * Tests that the class has exactly the expected public static properties.
     *
     * @return void
     */
    public function testExpectedStaticProperties(): void
    {
        $expected = ['name', 'description', 'help', 'type'];
        $staticProps = $this->reflection->getProperties(\ReflectionProperty::IS_STATIC);
        $staticPropNames = array_map(fn($p) => $p->getName(), $staticProps);
        sort($expected);
        sort($staticPropNames);
        $this->assertSame($expected, $staticPropNames);
    }

    /**
     * Tests that the class has the expected public methods.
     *
     * @return void
     */
    public function testExpectedPublicMethods(): void
    {
        $expected = ['__construct', 'getHooks', 'getMenu', 'getRequirements', 'getSettings'];
        $publicMethods = $this->reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $publicMethodNames = array_map(fn($m) => $m->getName(), $publicMethods);
        foreach ($expected as $method) {
            $this->assertContains($method, $publicMethodNames, "Method {$method} should be public");
        }
    }

    /**
     * Tests that getSettings extracts subject from event without error when given a simple subject.
     *
     * @return void
     */
    public function testGetSettingsExtractsSubjectFromEvent(): void
    {
        $subject = new \stdClass();
        $event = new GenericEvent($subject);
        // getSettings only does $event->getSubject(), so it should not throw
        Plugin::getSettings($event);
        $this->assertTrue(true, 'getSettings completed without error');
    }

    /**
     * Tests that getRequirements calls add_page_requirement on the subject.
     *
     * Uses an anonymous class to verify the loader interaction.
     *
     * @return void
     */
    public function testGetRequirementsCallsAddPageRequirement(): void
    {
        $called = false;
        $capturedArgs = [];

        $loader = new class($called, $capturedArgs) {
            /** @var bool */
            private $called;
            /** @var array */
            private $capturedArgs;

            public function __construct(bool &$called, array &$capturedArgs)
            {
                $this->called = &$called;
                $this->capturedArgs = &$capturedArgs;
            }

            public function add_page_requirement(string $name, string $path): void
            {
                $this->called = true;
                $this->capturedArgs = ['name' => $name, 'path' => $path];
            }
        };

        $event = new GenericEvent($loader);
        Plugin::getRequirements($event);

        $this->assertTrue($called, 'add_page_requirement should have been called');
        $this->assertSame('webuzo_configure', $capturedArgs['name']);
        $this->assertStringContainsString('webuzo_configure.php', $capturedArgs['path']);
    }
}
