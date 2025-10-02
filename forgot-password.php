<?php
session_start();
require_once 'models/User.php';

$errors = [];
$success = false;

if ($_POST) {
    $email = $_POST['email'] ?? '';
    
    if (empty($email)) {
        $errors[] = 'E-postadresse er påkrevd';
    } else {
        $user = User::findByEmail($email);
        if ($user) {
            $result = User::createPasswordResetToken($email);
            if ($result) {
                $success = true;
                $successMessage = 'En e-post med instruksjoner for å tilbakestille passordet er sendt til din e-postadresse.';
            } else {
                $errors[] = 'Feil ved sending av e-post';
            }
        } else {
            // Don't reveal if email exists or not for security
            $success = true;
            $successMessage = 'Hvis e-postadressen finnes i vårt system, vil du få en e-post med instruksjoner for å tilbakestille passordet.';
        }
    }
}

include 'views/forgot-password.php';
?>