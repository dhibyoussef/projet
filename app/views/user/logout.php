<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Store logout message before clearing session
$message = 'You have been successfully logged out.';
$message_type = 'success';

// Regenerate session ID for security
session_regenerate_id(true);

// Clear session data
$_SESSION = [];

// If it's desired to kill the session, also delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Start new session for the message
session_start();
$_SESSION['message'] = $message;
$_SESSION['message_type'] = $message_type;

// Redirect to login page
header("Location: /login");
exit();
?>