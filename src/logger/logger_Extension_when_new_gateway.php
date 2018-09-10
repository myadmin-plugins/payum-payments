<?php
use Payum\Core\Bridge\Psr\Log\LoggerExtension;
use Payum\Core\Tests\Mocks\Action\LoggerAwareAction;
use Payum\Core\Gateway;

$gateway = new Gateway;
$gateway->addExtension(new LoggerExtension($logger));
$gateway->addAction(new LoggerAwareAction);

$gateway->execute('a request');
