<?php

require 'vendor/autoload.php';
$ids = require 'paypal.php';

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        $ids['id'],
        $ids['secret']
        )
);


$payment = \PayPal\Api\Payment::get($_POST['paymentID'], $apiContext);
$execution = (new \PayPal\Api\PaymentExecution())
        ->setPayerId($_POST['payerID']);
try {
    $payment->execute($execution, $apiContext);
    $value = $payment->getAmount()[0]->getTotal();
    $userid = $payment->getTransactions()[0]->getCustom(); 
    echo json_encode([
        'id' => $payment->getId(),
        'valeur' => $value,
        'userid' => $userid,
    ]);


    //requete base de donnée ajouté $value au solde 
    /*$user = new Application_Model_DbTable_User();
    $auth = Zend_Auth::getInstance();
        $userId = $auth->getIdentity()->UserId;
    $userinfo = $user->fetchOne($userId);

    $solde = $userinfo['UserSolde'] + $value;
    $user->updateSolde($userId, $solde);*/
   

} catch (PayPal\Exception\PayPalConnectionException $e){
    header('HTTP 500 Internal Server Error', true, 500);
    var_dump(json_decode($e->getData()));
}