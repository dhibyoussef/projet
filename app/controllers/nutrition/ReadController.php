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
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to view nutrition data.";
    header('Location: ../../views/user/login.php');
    exit();
}

$nutritionModel = new NutritionModel($pdo);

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
    // Get nutrition logs for the logged-in user
    $nutritionLogs = $nutritionModel->getNutrition();
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Failed to retrieve nutrition data: ' . $e->getMessage();
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

include '../../views/nutrition/index.php';
?>