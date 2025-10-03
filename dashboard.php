<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'models/User.php';
$user = User::findById($_SESSION['user_id']);

include 'views/dashboard.php';
?>