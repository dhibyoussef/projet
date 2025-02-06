<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters before starting session
    $sessionParams = session_get_cookie_params();
    if (!session_set_cookie_params(
        $sessionParams["lifetime"],
        $sessionParams["path"],
        $sessionParams["domain"],
        true, // secure
        true  // httponly
    )) {
        $_SESSION['error_message'] = 'Failed to set session security parameters.';
        header('Location: ../../views/user/signup.php');
        exit();
    }
    
    if (!session_start()) {
        $_SESSION['error_message'] = 'Failed to start secure session.';
        header('Location: ../../views/user/signup.php');
        exit();
    }
    
    // Generate CSRF token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        if (!$_SESSION['csrf_token']) {
            $_SESSION['error_message'] = 'Failed to generate security token.';
            header('Location: ../../views/user/signup.php');
            exit();
        }
    }
}

require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

try {
    $userModel = new UserModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        // Enhanced CSRF token validation with timing attack protection
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'Security token missing. Please try again.';
            error_log("CSRF token missing for signup attempt from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Use hash_equals for secure comparison
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'Security token validation failed. Please try again.';
            error_log("CSRF token validation failed for signup attempt from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Regenerate session ID and CSRF token for security
        if (!session_regenerate_id(true)) {
            $_SESSION['error_message'] = 'Session security error. Please try again.';
            header('Location: ../../views/user/signup.php');
            exit();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Validate and sanitize input
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = trim($_POST['password'] ?? '');

        // Validate required fields
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error_message'] = 'All fields are required.';
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Validate password strength
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['error_message'] = 'Password must be at least 8 characters long and contain at least one uppercase letter and one number.';
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Check if email exists
        if ($userModel->emailExists($email)) {
            $_SESSION['error_message'] = 'This email is already registered.';
            header('Location: ../../views/user/signup.php');
            exit();
        }

        // Store password exactly as entered without any modification
        $exactPassword = $password;

        // Create user with the exact password (no hashing)
        if ($userModel->createUser($name, $email, $exactPassword)) {
            $_SESSION['success_message'] = 'Signup successful! Please login.';
            header('Location: ../../views/user/login.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Failed to create account. Please try again.';
            header('Location: ../../views/user/signup.php');
            exit();
        }
    }

} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database error. Please try again later.';
    error_log('Database error during signup: ' . $e->getMessage());
    header('Location: ../../views/user/signup.php');
    exit();
} catch (Exception $e) {
    $_SESSION['error_message'] = 'An unexpected error occurred. Please try again.';
    error_log('Signup error: ' . $e->getMessage());
    header('Location: ../../views/user/signup.php');
    exit();
}
?>