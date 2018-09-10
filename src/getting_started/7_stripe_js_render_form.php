<?php

try {
	$gateway->execute(new \Payum\Core\Request\Capture($model));
} catch (Payum\Core\Reply\ReplyInterface $reply) {
	if ($reply instanceof Payum\Core\Reply\HttpResponse) {
		echo $reply->getContent();

		exit;
	}

	throws \LogicException('Unsupported reply', null, $reply);
}
