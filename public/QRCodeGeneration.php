<?php
/*
 * Script de création du QRcode
 */

require_once '../library/phpqrcode/qrlib.php';

// Récupération de l'id du ticket
$ticket = $_GET['ticket'];

// Creation des variables
$qrValue = 'test'; // à completer

// Affichage du QRcode
QRcode::png($qrValue);
