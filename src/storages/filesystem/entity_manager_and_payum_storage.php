<?php
use Payum\Core\Storage\FilesystemStorage;
$storage = new FilesystemStorage(
    '/path/to/storage', 
    'Payum\Core\Model\Payment', 
    'number'
);
