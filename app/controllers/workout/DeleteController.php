<?php
try {
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
    require_once '../../models/WorkoutModel.php';
    require_once '../../../config/database.php';

    $workoutModel = new WorkoutModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
        // Enhanced CSRF token validation with timing attack protection
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Log the CSRF token validation failure
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
            
            // Clear the invalid CSRF token
            unset($_SESSION['csrf_token']);
            
            echo json_encode([
                'status' => 'error', 
                'message' => 'Security token validation failed. Please try again.',
                'animation' => 'shake'
            ]);
            exit();
        }

        // Regenerate session ID and CSRF token for security
        if (!session_regenerate_id(true)) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Session security regeneration failed',
                'animation' => 'fade'
            ]);
            exit();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Invalid workout ID.',
                'animation' => 'shake'
            ]);
            exit();
        }

        if (!$workoutModel->deleteWorkout($id, $_SESSION['user_id'])) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Failed to delete workout.',
                'animation' => 'fade'
            ]);
            exit();
        }

        echo json_encode([
            'status' => 'success', 
            'message' => 'Workout deleted successfully!',
            'animation' => 'fade'
        ]);
        exit();
    }
} catch (Exception $e) {
    // Log the error with additional context
    error_log("Error in DeleteController: " . $e->getMessage() . " | Code: " . $e->getCode());
    
    // Return JSON response with error message
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage(),
        'animation' => 'shake'
    ]);
    exit();
}
?>