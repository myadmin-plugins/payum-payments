<?php
use Payum\Core\Storage\FilesystemStorage;
$storage = new FilesystemStorage('/path/to/storage', 'Payum\Core\Model\Payment', 'number');
$order = $storage->create();
$order->setTotalAmount(123);
$order->setCurrency('EUR');
$storage->update($order);
$foundOrder = $storage->find($order->getNumber());
