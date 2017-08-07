<?php
use Payum\Core\Bridge\Psr\Log\LogExecutedActionsExtension;
use Payum\Core\Tests\Mocks\Action\CaptureAction;
use Payum\Core\Gateway;
use Payum\Core\Request\Capture;

$gateway = new Gateway;
$gateway->addExtension(new LogExecutedActionsExtension($logger));
$gateway->addAction(new CaptureAction);

$gateway->execute(new Capture($model = new \stdClass));
