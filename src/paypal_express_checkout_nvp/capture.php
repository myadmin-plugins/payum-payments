<?php
//capture.php
include 'config.php';
$payum->getHttpController()->capture();
if ($reply = $gateway->execute(new Capture($token), true)) {
    if ($reply instanceof HttpRedirect) {
        header('Location: ' .$reply->getUrl());
        die();
    }
    throw new \LogicException('Unsupported reply', null, $reply);
}
$payum->getRequestVerifier()->invalidate($token);
header('Location: ' .$token->getAfterUrl());
