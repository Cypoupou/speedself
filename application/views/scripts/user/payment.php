<?php

require 'vendor/autoload.php';
$ids = require('paypal.php');
$value = json_decode($_POST['value']);

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        $ids['id'],
        $ids['secret']
    )
);

$name = "rechargement";
$price = $value;

$payment = new \PayPal\Api\Payment();
$payment->setIntent('sale');
$redirectUrls = (new \PayPal\Api\RedirectUrls())
    ->setReturnUrl('http://localhost/speedself/application/views/scripts/user/pay.php')
    ->setCancelUrl('http://localhost/speedself/application/views/scripts/user/recharger.phtml');
$payment->setRedirectUrls($redirectUrls);
$payment->setPayer((new \PayPal\Api\Payer())->setPaymentMethod('paypal'));

$list = new \PayPal\Api\ItemList();
$item = (new PayPal\Api\Item())
        ->setName($name)
        ->setPrice($price)
        ->setCurrency("EUR")
        ->setQuantity(1);
$list->addItem($item);

$details = (new \PayPal\Api\Details())
        ->setSubtotal($price);

$amount = (new \PayPal\Api\Amount())
    ->setTotal($price)
    ->setCurrency("EUR")
    ->setDetails($details);

$transaction = (new \PayPal\Api\Transaction())
    ->setItemList($list)
    ->setDescription('rechargement compte SpeedSelf')
    ->setAmount($amount)
    ->setCustom($value);

$payment->setTransactions([$transaction]);

try {  
    $payment->create($apiContext);   
    echo json_encode([
        'id' => $payment->getId()
    ]);
} catch (PayPal\Exception\PayPalConnectionException $e){
    var_dump(json_decode($e->getData()));
}

