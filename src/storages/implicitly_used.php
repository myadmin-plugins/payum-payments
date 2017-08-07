<?php
use Payum\Core\Extension\StorageExtension;
use Payum\Core\Gateway;
use Payum\Core\Storage\FilesystemStorage;
$gateway->addExtension(new StorageExtension(
   new FilesystemStorage('/path/to/storage', 'Payum\Core\Model\Payment', 'number')
));
