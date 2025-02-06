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

// Check if user is logged in using consistent session variable
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error_message'] = "You must be logged in to view workouts.";
    header('Location: /login');
    exit();
}

require_once '../BaseController.php';
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

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
    $workoutModel = new WorkoutModel($pdo);
    // Get workouts for the logged-in user using their session ID
    $workouts = $workoutModel->getWorkoutsByUserId($_SESSION['user_id']);
    
    // Store workouts in session for potential use in other requests
    $_SESSION['workouts'] = $workouts;
    
    if (file_exists('../../views/workout/index.php')) {
        include '../../views/workout/index.php';
    } else {
        header('Location: ../../views/errors/404.php');
        exit();
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $_SESSION['error_message'] = 'Database error occurred.';
    header('Location: ../../views/errors/500.php');
    exit();
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while fetching workouts.';
    header('Location: ../../views/errors/500.php');
    exit();
}
?>