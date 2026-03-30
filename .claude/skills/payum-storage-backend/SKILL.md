---
name: payum-storage-backend
description: Configures Payum storage backends (Doctrine ORM, MongoDB ODM, Filesystem, Propel2) including Payment/PaymentToken models and DoctrineStorage wiring. Use when user says 'add storage', 'configure doctrine', 'new storage backend', 'payment model', or modifies files in src/storages/. Do NOT use for gateway config, capture flows, or recurring payment setup.
---
# Payum Storage Backend

## Critical

- **Never mix ORM and ODM models** — ORM models live under `Acme\Entity\`, ODM under `Acme\Document\`. Using the wrong namespace causes Doctrine mapping failures.
- **`DoctrineStorage` requires a fully-qualified class name string** as its second argument — not an object or short name.
- **Filesystem storage requires a unique identity field** as its third argument (`'number'` for Payment, omit for Token which uses hash).
- **MongoDB ODM requires `Type::addType('object', ...)`** before building the document manager or Payum will fail to serialize payment details.
- **Propel2 custom storage must implement every method** of `StorageInterface` — partial implementations throw fatal errors at runtime.

## Instructions

### Step 1 — Choose a storage backend

Identify which backend is needed:

| Backend | Use when |
|---|---|
| Doctrine ORM | MySQL/PostgreSQL with Doctrine |
| MongoDB ODM | MongoDB with Doctrine ODM |
| Filesystem | Dev/testing, no DB required |
| Propel2 | Existing Propel2 schema |

Verify the required packages are present in `composer.json` before proceeding:
- ORM: doctrine/orm, payum/core (bridge included)
- ODM: doctrine/mongodb-odm, payum/core
- Filesystem: payum/core only
- Propel2: propel/propel

### Step 2 — Create Payment model (ORM or ODM only)

**Doctrine ORM** → `src/storages/doctrine_orm/Payment.php`:
```php
<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Payment extends BasePayment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;
}
```

**MongoDB ODM** → `src/storages/doctrine_mongoodm/Payment.php`:
```php
<?php

namespace Acme\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @Mongo\Document
 */
class Payment extends BasePayment
{
    /**
     * @Mongo\Id
     *
     * @var integer $id
     */
    protected $id;
}
```

Verify: class extends `BasePayment`, has `protected $id` with correct annotation driver for the backend.

### Step 3 — Create PaymentToken model (ORM or ODM only)

**Doctrine ORM** → `src/storages/doctrine_orm/PaymentToken.php`:
```php
<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class PaymentToken extends Token
{
}
```

**MongoDB ODM** → `src/storages/doctrine_mongoodm/PaymentToken.php`:
```php
<?php

namespace Acme\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as Mongo;
use Payum\Core\Model\Token;

/**
 * @Mongo\Document
 */
class PaymentToken extends Token
{
}
```

Verify: class extends `Token` (not `BasePayment`), correct namespace matches Step 2.

### Step 4 — Wire entity manager and storage

**Doctrine ORM** → `src/storages/doctrine_orm/entity_manager_and_payum_storage.php`:
```php
<?php

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

$config = new Configuration();
$driver = new MappingDriverChain();
// payum's basic models
$driver->addDriver(
    new SimplifiedXmlDriver(['path/to/Payum/Core/Model' => 'Payum\Core\Model']),
    'Payum\Core\Model'
);
// your models
$driver->addDriver(
    $config->newDefaultAnnotationDriver(['path/to/Acme/Entity'], false),
    'Acme\Entity'
);
$config->setAutoGenerateProxyClasses(true);
$config->setProxyDir(\sys_get_temp_dir());
$config->setProxyNamespace('Proxies');
$config->setMetadataDriverImpl($driver);
$config->setQueryCacheImpl(new ArrayCache());
$config->setMetadataCacheImpl(new ArrayCache());
$connection = ['driver' => 'pdo_sqlite', 'path' => ':memory:'];
$orderStorage = new DoctrineStorage(
    EntityManager::create($connection, $config),
    'Payum\Entity\Payment'
);
$tokenStorage = new DoctrineStorage(
    EntityManager::create($connection, $config),
    'Payum\Entity\PaymentToken'
);
```

**MongoDB ODM** → `src/storages/doctrine_mongoodm/entity_manager_and_payum_storage.php`:
```php
<?php

use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

Type::addType('object', 'Payum\Core\Bridge\Doctrine\Types\ObjectType');
// ... driver chain setup (see src/storages/doctrine_mongoodm/entity_manager_and_payum_storage.php)
$orderStorage = new DoctrineStorage(
    DocumentManager::create($connection, $config),
    'Acme\Document\Payment'
);
$tokenStorage = new DoctrineStorage(
    DocumentManager::create($connection, $config),
    'Acme\Document\SecurityToken'
);
```

**Filesystem** → `src/storages/filesystem/entity_manager_and_payum_storage.php`:
```php
<?php

use Payum\Core\Storage\FilesystemStorage;

$storage = new FilesystemStorage(
    '/path/to/storage',
    'Payum\Core\Model\Payment',
    'number'
);
```

Verify: `FilesystemStorage` third arg (`'number'`) is the identity field on the model.

### Step 5 — Use storage explicitly or via extension

**Explicit use** (create, update, find):
```php
$order = $storage->create();
$order->setTotalAmount(123);
$order->setCurrency('EUR');
$storage->update($order);
$foundOrder = $storage->find($order->getNumber());
```

**Implicit use via StorageExtension** (auto-resolves models during gateway execute):
```php
use Payum\Core\Extension\StorageExtension;

$gateway->addExtension(new StorageExtension($storage));
```

**Identity-based capture** (pass identity instead of model to avoid eager loading):
```php
use Payum\Core\Request\Capture;

$gateway->execute(new Capture($storage->identify($order)));
```

Verify: run `vendor/bin/phpunit tests/StorageModelsTest.php` — all tests green before proceeding.

### Step 6 — Propel2 custom storage (if applicable)

`src/storages/propel2/connection.php` — configure connection:
```php
use Propel\Runtime\Propel;
use Propel\Runtime\Connection\ConnectionManagerSingle;

$serviceContainer = Propel::getServiceContainer();
$serviceContainer->setAdapterClass('default', 'mysql');
$manager = new ConnectionManagerSingle();
$manager->setConfiguration([
    'dsn'      => 'mysql:host=localhost;dbname=my_db_name',
    'user'     => 'my_db_user',
    'password' => 's3cr3t',
]);
$serviceContainer->setConnectionManager('default', $manager);
```

`src/storages/propel2/custom_storage.php` — implement `StorageInterface`:
```php
use Payum\Core\Storage\StorageInterface;

class CustomStorage implements StorageInterface
{
    // implement ALL declared methods: create, find, update, delete, identify, findBy
}
```

Verify: `StorageInterface` has no optional methods — missing any causes a fatal `PHP Fatal error: Class CustomStorage contains abstract methods`.

## Examples

**User says:** "Add Doctrine ORM storage for payments"

**Actions taken:**
1. Create `src/storages/doctrine_orm/Payment.php` with `namespace Acme\Entity`, extends `BasePayment`, `protected $id` with `@ORM` annotations.
2. Create `src/storages/doctrine_orm/PaymentToken.php` with `namespace Acme\Entity`, extends `Token`, `@ORM\Table @ORM\Entity`.
3. Create `src/storages/doctrine_orm/entity_manager_and_payum_storage.php` with `MappingDriverChain` for both `Payum\Core\Model` (XML driver) and `Acme\Entity` (annotation driver). Wire `$orderStorage` and `$tokenStorage` as `DoctrineStorage` instances.
4. Run `vendor/bin/phpunit tests/StorageModelsTest.php tests/StorageScriptsTest.php`.

**Result:** Two `DoctrineStorage` instances (`$orderStorage`, `$tokenStorage`) ready to pass to `PayumBuilder`.

## Common Issues

**`PHP Fatal error: Class 'Proxies\...' not found`**
- ORM proxy classes not generated. Ensure `$config->setAutoGenerateProxyClasses(true)` is set, or run `doctrine orm:generate-proxies`.

**`Payum\Core\Exception\LogicException: Unsupported reply`** during capture with identity
- Passed a raw model to `Capture` instead of `$storage->identify($order)`. Use `new Capture($storage->identify($order))`.

**`Type 'object' not found`** (MongoDB ODM)
- Missing `Type::addType('object', 'Payum\Core\Bridge\Doctrine\Types\ObjectType')` before building the `DocumentManager`. Must be called once before any ODM initialization.

**`FilesystemStorage` throws on `find()`**
- The identity field passed as third constructor argument doesn't match the getter on the model. For `Payum\Core\Model\Payment`, use `'number'` (calls `getNumber()`).

**`CustomStorage` fatal on instantiation (Propel2)**
- `StorageInterface` method not implemented. Check `vendor/payum/core/Payum/Core/Storage/StorageInterface.php` for the full method list and implement every one.

**Tests fail: `StorageScriptsTest` — file not found**
- The storage script file path registered in `tests/StorageScriptsTest.php` doesn't match `src/storages/...` filename. Verify exact filename and directory match what the test expects via `FileExistenceTest`.
