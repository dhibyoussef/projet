<?php
require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

// Check if session is not already active before starting
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters
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

$userModel = new UserModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Validate CSRF token
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/user/login.php');
        exit();
    }

    // Regenerate session ID for security on POST requests
    session_regenerate_id(true);

    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    try {
        // First get the user by email to retrieve the stored password
        $user = $userModel->getUserByEmail($email);
        
        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Store minimal user information in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();
                
                // Set success message
                $_SESSION['success_message'] = 'Login successful!';
                header('Location: ../../views/user/profile.php');
                exit();
            } else {
                // Handle incorrect password
                $_SESSION['error_message'] = 'Invalid password.';
                header('Location: ../../views/user/login.php');
                exit();
            }
        } else {
            // Handle user not found
            $_SESSION['error_message'] = 'User not found.';
            header('Location: ../../views/user/login.php');
            exit();
        }
    } catch (Exception $e) {
        // Handle the exception with more specific error message
        $_SESSION['error_message'] = 'Login failed: ' . $e->getMessage();
        error_log('Login error: ' . $e->getMessage());
        header('Location: ../../views/user/login.php');
        exit();
    }
}
?>