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

// Check if user is logged in using consistent session variable
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error_message'] = "You must be logged in to view workouts.";
    $_SESSION['error_animation'] = 'windowShake';
    header('Location: /login');
    exit();
}

require_once '../BaseController.php';
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

// Validate CSRF token for POST requests using timing-safe comparison
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        $_SESSION['error_animation'] = 'windowShake';
    }
    
    // Regenerate session ID and CSRF token for security on POST requests
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }

    $workoutModel = new WorkoutModel($pdo);
    
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User ID not found in session');
    }

    // Get workouts for the logged-in user using their session ID
    $workouts = $workoutModel->getWorkoutsByUserId($_SESSION['user_id']);
    
    if ($workouts === false) {
        throw new Exception('Failed to retrieve workouts');
    }
    
    // Store workouts in session for potential use in other requests
    $_SESSION['workouts'] = $workouts;
    
    $viewPath = '../../views/workout/index.php';
    if (!file_exists($viewPath)) {
        throw new Exception("View file not found: $viewPath");
    }
    
    // Pass error message and animation to view if they exist
    $errorMessage = $_SESSION['error_message'] ?? null;
    $errorAnimation = $_SESSION['error_animation'] ?? null;
    unset($_SESSION['error_message']);
    unset($_SESSION['error_animation']);
    
    include $viewPath;
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Database error occurred. Please try again later.';
    $_SESSION['error_animation'] = 'windowShake';
    include '../../views/workout/index.php';
} catch (InvalidArgumentException $e) {
    error_log("Invalid argument error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Invalid request. Please check your input.';
    $_SESSION['error_animation'] = 'windowShake';
    include '../../views/workout/index.php';
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error_message'] = 'An unexpected error occurred: ' . $e->getMessage();
    $_SESSION['error_animation'] = 'windowShake';
    include '../../views/workout/index.php';
}
?>