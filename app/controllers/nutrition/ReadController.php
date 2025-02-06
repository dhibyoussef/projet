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
    
    // Generate CSRF token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';
require_once '../../../config/error.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    logError('Unauthorized access attempt to nutrition view');
    $_SESSION['error_message'] = "You must be logged in to view nutrition.";
    $_SESSION['error_animation'] = 'shake';
    header('Location: /login');
    exit();
}

$nutritionModel = new NutritionModel($pdo);

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        logError('CSRF token validation failed in nutrition read controller');
        $_SESSION['error_message'] = "CSRF token validation failed.";
        $_SESSION['error_animation'] = 'shake';
    } else {
        // Regenerate session ID and CSRF token for security on POST requests
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

try {
    // Get nutrition logs for the logged-in user
    $nutritionLogs = $nutritionModel->getNutrition();
} catch (Exception $e) {
    $errorMessage = 'Failed to retrieve nutrition data: ' . $e->getMessage();
    logError($errorMessage);
    $_SESSION['error_message'] = $errorMessage;
    $_SESSION['error_animation'] = 'shake';
}

// Prepare messages for view
$messages = [];
if (isset($_SESSION['success_message'])) {
    $messages['success'] = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $messages['error'] = $_SESSION['error_message'];
    $messages['animation'] = $_SESSION['error_animation'] ?? '';
    unset($_SESSION['error_message']);
    unset($_SESSION['error_animation']);
}

// Pass CSRF token and messages to view
$csrf_token = $_SESSION['csrf_token'];

// Include view with error handling
try {
    include '../../views/nutrition/index.php';
} catch (Exception $e) {
    $errorMessage = 'Failed to load nutrition view: ' . $e->getMessage();
    logError($errorMessage);
    $_SESSION['error_message'] = 'An error occurred while loading the page.';
    $_SESSION['error_animation'] = 'shake';
    include '../../views/nutrition/index.php';
}
?>