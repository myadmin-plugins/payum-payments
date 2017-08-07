<?php
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Doctrine\ODM\MongoDB\Types\Type;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\MongoDB\Mapping\Driver\XmlDriver;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\MongoDB\Connection;
Type::addType('object', 'Payum\Core\Bridge\Doctrine\Types\ObjectType');
$driver = new MappingDriverChain;
// payum's basic models
$driver->addDriver(
    new XmlDriver(
       new SymfonyFileLocator(
	       [
            '/path/to/Payum/Core/Bridge/Doctrine/Resources/mapping' => 'Payum\Core\Model'
	       ], '.mongodb.xml'),
        '.mongodb.xml'
    ),
    'Payum\Core\Model'
);
// your models
AnnotationDriver::registerAnnotationClasses();
$driver->addDriver(
    new AnnotationDriver(new AnnotationReader(), [
        'path/to/Acme/Document'
                                               ]
    ),
    'Acme\Document'
);
$config = new Configuration();
$config->setProxyDir(\sys_get_temp_dir());
$config->setProxyNamespace('Proxies');
$config->setHydratorDir(\sys_get_temp_dir());
$config->setHydratorNamespace('Hydrators');
$config->setMetadataDriverImpl($driver);
$config->setMetadataCacheImpl(new ArrayCache());
$config->setDefaultDB('payum_tests');
$connection = new Connection(null, [], $config);
$orderStorage = new DoctrineStorage(
    DocumentManager::create($connection, $config),
    'Acme\Document\Payment'
);
$tokenStorage = new DoctrineStorage(
    DocumentManager::create($connection, $config),
    'Acme\Document\SecurityToken'
);
