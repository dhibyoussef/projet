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
    
    // Generate CSRF token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

$userModel = new UserModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Validate CSRF token using hash_equals for timing attack protection
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_message'] = 'Security token validation failed. Please try again.';
        header('Location: ../../views/user/login.php');
        exit();
    }

    // Regenerate session ID and CSRF token for security on POST requests
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Validate and sanitize input
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['error_message'] = 'Email and password are required.';
        header('Location: ../../views/user/login.php');
        exit();
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Invalid email format.';
        header('Location: ../../views/user/login.php');        
        exit();
    }
    $password = $_POST['password']; // Keep password exactly as entered

    // First get the user by email to retrieve the stored password
    $user = $userModel->getUserByEmail($email);
    
    if (!$user) {
        // Log failed login attempt
        error_log("Login attempt for non-existent email: $email");
        $_SESSION['error_message'] = 'Invalid credentials.';
        header('Location: ../../views/user/login.php');        
        exit();
    }

    // Verify the password using password_verify() for hashed passwords
    if (!password_verify($password, $user['password'])) {
        // Log failed password attempt
        error_log("Failed password attempt for user: " . $user['id']);
        $_SESSION['error_message'] = 'Invalid credentials.';
        header('Location: ../../views/user/login.php');
        exit();
    }

    // Store minimal user information in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['logged_in'] = true;
    $_SESSION['last_activity'] = time();
    
    // Set success message and redirect
    $_SESSION['success_message'] = 'Login successful!';
    header('Location:../../user/profile.php?id=' . $_SESSION['user_id']);
    exit();
}
?>