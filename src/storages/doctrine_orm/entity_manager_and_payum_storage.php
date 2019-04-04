<?php
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\SimplifiedXmlDriver;
use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

$config = new Configuration();
$driver = new MappingDriverChain;
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
