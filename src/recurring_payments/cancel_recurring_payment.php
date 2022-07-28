<?php

use Payum\Core\Request\Cancel;
use Payum\Core\Request\Sync;
use Payum\Core\Request\GetHumanStatus;

$gateway->execute(new Cancel($recurringPayment));
$gateway->execute(new Sync($recurringPayment));

$gateway->execute($status = new GetHumanStatus($recurringPayment));

if ($status->isCanceled()) {
    // yes it is cancelled
} else {
    // hm... not yet
}
