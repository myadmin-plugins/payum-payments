<?php


try {
    $gateway->execute(new \Payum\Core\Request\Capture($model));
} catch (Payum\Core\Reply\ReplyInterface $reply) {
    if ($reply instanceof Payum\Core\Reply\HttpRedirect) {
        header('Location: ' .$reply->getUrl());

        exit;
    }

    throw new \LogicException('Unsupported reply', null, $reply);
}
