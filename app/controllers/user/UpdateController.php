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

$userModel = new UserModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Validate session and CSRF token
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/error.php');
        exit;
    }

    // Regenerate session ID for security
    session_regenerate_id(true);

    // Validate and sanitize input
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validate required fields
    if (!$id || empty($name) || empty($email)) {
        $_SESSION['error_message'] = 'All fields are required.';
        header('Location: ../../views/user/profile.php');
        exit;
    }

    // Hash the password if it's being updated
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    try {
        if ($userModel->updateUser($id, $name, $email, $hashedPassword)) {
            // Update session email if it's the current user
            if ($id == $_SESSION['user_id']) {
                $_SESSION['email'] = $email;
            }
            
            // Set success message in session
            $_SESSION['success_message'] = 'Profile updated successfully!';
            header('Location: ../../views/user/profile.php');
            exit;
        } else {
            throw new Exception('Failed to update profile data.');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../../views/user/profile.php');
        exit;
    }
} else {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: ../../views/user/profile.php');
    exit;
}
?>