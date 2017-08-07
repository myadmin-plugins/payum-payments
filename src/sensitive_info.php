<?php

$cardNumber = new SensitiveValue('theCreditCardNumber');

serialize($cardNumber);
//null

clone $cardNumber;
// exception

$cardNumber->erase();
// remove value forever.

$cardNumber->get();
// get sensitive value and erase it

$cardNumber->peek();
// get sensitive value but do not erase it. use this method carefully

(string) $model['cardNumber'];
// empty string

json_encode($model['cardNumber']);
// {}
