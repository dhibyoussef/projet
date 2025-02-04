<?php
require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page
    header('Location: ../../index.php');
    exit();
} else {
    // If no user is logged in, redirect to the login page
    header('Location: ../../index.php');
    exit();
}
?>