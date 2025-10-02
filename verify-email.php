<?php
session_start();
require_once 'models/User.php';

$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $result = User::verifyEmail($token);
    
    if ($result) {
        $success = true;
        $message = 'E-posten din er nå verifisert! Du kan nå logge inn på kontoen din.';
    } else {
        $message = 'Ugyldig eller utløpt verifikasjonstoken. Be om ny verifikasjonsepost.';
    }
} else {
    $message = 'Mangler verifikasjonstoken.';
}

include 'views/verify-email.php';
?>