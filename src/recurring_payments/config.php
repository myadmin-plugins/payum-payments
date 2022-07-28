<?php

//config.php

$agreementClass = 'App\Model\AgreementDetails';
$recurringPaymentClass = 'App\Model\RecurringPaymentDetails';

$storages[$agreementClass] = new FilesystemStorage(
    __DIR__.'/storage',
    $agreementClass
);
$storages[$recurringPaymentClass] = new FilesystemStorage(
    __DIR__.'/storage',
    $recurringPaymentClass
);
