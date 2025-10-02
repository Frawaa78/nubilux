<?php
session_start();
require_once 'models/User.php';

$error = '';

if ($_POST) {
    $user = User::verifyPassword($_POST['email'], $_POST['password']);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Ugyldig e-post eller passord';
    }
}

include 'views/login.php';
?>