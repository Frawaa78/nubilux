<?php
session_start();
require_once 'models/User.php';

$errors = [];
$success = false;

if ($_POST) {
    // Validate input
    if (empty($_POST['first_name'])) $errors[] = "Fornavn er påkrevd";
    if (empty($_POST['last_name'])) $errors[] = "Etternavn er påkrevd";
    if (empty($_POST['email'])) $errors[] = "E-post er påkrevd";
    if (empty($_POST['password'])) $errors[] = "Passord er påkrevd";
    if ($_POST['password'] !== $_POST['confirm_password']) $errors[] = "Passordene matcher ikke";
    
    // Check if email exists
    if (User::findByEmail($_POST['email'])) {
        $errors[] = "E-post er allerede registrert";
    }
    
    if (empty($errors)) {
        $userId = User::create($_POST);
        if ($userId) {
            $success = true;
            $successMessage = "Konto opprettet! En verifikasjons-e-post er sendt til din e-postadresse. Sjekk innboksen din og klikk på lenken for å verifisere kontoen.";
        } else {
            $errors[] = "Feil ved registrering";
        }
    }
}

include 'views/register.php';
?>