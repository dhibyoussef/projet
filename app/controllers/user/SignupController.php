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

try {
    $userModel = new UserModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        // Validate CSRF token
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error_message'] = "CSRF token validation failed.";
            header('Location: ../../views/errors/403.php');
            exit();
        }

        // Regenerate session ID for security
        session_regenerate_id(true);

        // Validate and sanitize input
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Validate required fields
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'All fields are required.';
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Validate password strength
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['error_message'] = "Password must be at least 8 characters long, contain at least one uppercase letter and one number.";
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Check if email exists
        if ($userModel->emailExists($email)) {
            $_SESSION['error_message'] = "Email already exists.";
            header('Location: ../../views/user/signup.php');
            exit();
        } else {
            // Store the password as-is without hashing
            if ($userModel->createUser($name, $email, $password)) {
                // Set success message in session
                $_SESSION['success_message'] = 'Signup successful! Please login.';
                header('Location: ../../views/user/login.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Failed to create user account.';
                header('Location: ../../views/user/signup.php');
                exit();
            }
        }
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database connection failed: ' . $e->getMessage();
    header('Location: ../../views/errors/500.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Error during signup: ' . $e->getMessage();
    header('Location: ../../views/errors/500.php');
    exit();
}
?>