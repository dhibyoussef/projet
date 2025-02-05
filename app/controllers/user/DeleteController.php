<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters before starting session
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params(
        $sessionParams["lifetime"],
        $sessionParams["path"],
        $sessionParams["domain"],
        true, // secure
        true  // httponly
    );
    session_start();
}

require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to delete a user.";
    header('Location: ../../views/user/login.php');
    exit();
}

$userModel = new UserModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    // Validate CSRF token
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/errors/403.php');
        exit();
    }

    // Regenerate session ID for security on POST requests
    session_regenerate_id(true);

    // Get and validate input data
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['error_message'] = 'Invalid user ID.';
        header('Location: ../../views/user/signup.php');
        exit();
    }

    try {
        if ($userModel->deleteUser($id)) {
            $_SESSION['success_message'] = 'User deleted successfully!';
            header('Location: ../../views/user/signup.php');
            exit();
        } else {
            throw new Exception('Failed to delete user.');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../../views/user/signup.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: ../../views/user/signup.php');
    exit();
}