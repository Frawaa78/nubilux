<?php
session_start();
require_once 'models/User.php';

$errors = [];

if ($_POST) {
    $user = User::verifyPassword($_POST['email'], $_POST['password']);
    if ($user) {
        // Check if user is verified
        if ($user['is_verified'] == 0) {
            $errors[] = 'Du må verifisere e-postadressen din før du kan logge inn. Sjekk innboksen din.';
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            header('Location: dashboard.php');
            exit;
        }
    } else {
        $errors[] = 'Ugyldig e-post eller passord';
    }
}

include 'views/login-clean.php';
?>