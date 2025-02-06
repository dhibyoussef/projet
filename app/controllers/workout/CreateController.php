<?php
try {
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
            throw new Exception('Failed to set session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }

    require_once '../BaseController.php';
    require_once '../../models/WorkoutModel.php';
    require_once '../../../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: ../../views/user/login.php');
        exit();
    }

    $workoutModel = new WorkoutModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
        // Enhanced CSRF token validation with timing attack protection
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Log the CSRF token validation failure with detailed context
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
                     " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
                     " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
            
            // Clear the invalid CSRF token
            unset($_SESSION['csrf_token']);
            
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Regenerate session ID and CSRF token for security on POST requests
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Validate and sanitize input
        $workoutName = htmlspecialchars($_POST['workoutName'] ?? '');
        $workoutDescription = htmlspecialchars($_POST['workoutDescription'] ?? '');
        $workoutDuration = htmlspecialchars($_POST['workoutDuration'] ?? '');

        // Basic input validation
        if (empty($workoutName) || empty($workoutDuration)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $data = [
            'id' => $_SESSION['user_id'],
            'workout_name' => $workoutName,
            'description' => $workoutDescription,
            'duration' => $workoutDuration
        ];

        if (!$workoutModel->createWorkout($data, $_SESSION['user_id'])) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    include "../../views/workout/create.php";

} catch (Exception $e) {
    // Log the error with additional context
    error_log("Error in CreateController: " . $e->getMessage() . 
             " | User ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
             " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    if ($e->getMessage() === "You must be logged in to create a workout.") {
        header('Location: ../../views/user/login.php');
        exit();
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>