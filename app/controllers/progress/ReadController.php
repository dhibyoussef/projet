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
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error_message'] = "You must be logged in to view progress.";
    header('Location: /login');
    exit();
}


$progressModel = new ProgressModel($pdo);

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/errors/403.php');
        exit();
    }
    // Regenerate session ID for security on POST requests
    session_regenerate_id(true);
}

try {
    // Get progress logs for the logged-in user
    $progressLogs = $progressModel->getAllProgress();
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Failed to retrieve progress data: ' . $e->getMessage();
    header('Location: ../../views/errors/500.php');
    exit();
}

// Display any success/error messages from previous operations
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

include '../../views/progress/index.php';
?>