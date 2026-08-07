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
     * Every source getRequirements() registers must resolve to a file that exists.
     *
     * This replaces testGetRequirementsCallsAddPageRequirement, which asserted that
     * getRequirements() registered the name `webuzo_configure` at a path containing
     * `webuzo_configure.php`. That test was green for years while the file it named had
     * never existed in this package -- the registration was copy-pasted from
     * myadmin-webuzo-vps. It checked the registration table and never the filesystem, so it
     * locked the bug in place rather than catching it, and it was deleted along with the
     * registration rather than adjusted.
     *
     * The registration created a router route, so `/webuzo_configure` would have been a 500
     * had this plugin's hooks not been entirely commented out. `include/tf.php` require_once's
     * a requirement source with no file_exists guard, so a wrong path is a fatal.
     *
     * The table is empty now, so this passes vacuously -- which is the correct outcome, not a
     * gap. The closing assertion keeps the test from being reported risky under
     * failOnRisky when the loop body never runs.
     *
     * @return void
     */
    public function testEveryRegisteredRequirementSourceResolvesToAnExistingFile(): void
    {
        $registered = [];

        $loader = new class($registered) {
            /** @var array */
            private $registered;

            public function __construct(array &$registered)
            {
                $this->registered = &$registered;
            }

            public function add_requirement(string $name, string $path, $methods = false): void
            {
                $this->registered[] = [$name, $path];
            }

            public function add_page_requirement(string $name, string $path, $methods = false): void
            {
                $this->registered[] = [$name, $path];
            }
        };

        Plugin::getRequirements(new GenericEvent($loader));

        $packageRoot = dirname(__DIR__);
        $prefix = '/../vendor/detain/myadmin-payum-payments/';
        $missing = [];
        foreach ($registered as [$name, $source]) {
            // Sources are written relative to the host's INCLUDE_ROOT. Resolve a
            // self-referencing one against the package root so this test also means
            // something in a standalone checkout with no core around it.
            if (strpos($source, $prefix) === 0) {
                $resolved = $packageRoot . '/' . substr($source, strlen($prefix));
            } else {
                $resolved = dirname($packageRoot, 4) . '/include/' . ltrim($source, '/');
            }
            if (!is_file($resolved)) {
                $missing[] = $name . ' -> ' . $resolved;
            }
        }

        $this->assertSame([], $missing, 'getRequirements() registered sources that do not exist');
        $this->assertIsArray($registered);
    }
}
