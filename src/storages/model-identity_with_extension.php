<?php

use Payum\Core\Extension\StorageExtension;
use Payum\Core\Model\Identity;
use Payum\Core\Storage\FilesystemStorage;
use Payum\Core\Gateway;
use Payum\Core\Request\Capture;

$storage = new FilesystemStorage('/path/to/storage', 'Payum\Core\Model\Payment', 'number');
$order = $storage->create();
$storage->update($order);
$gateway->addExtension(new StorageExtension($storage));
$gateway->execute($capture = new Capture(
    $storage->identify($order)
));
echo get_class($capture->getModel());
// -> Payum\Core\Model\Payment
