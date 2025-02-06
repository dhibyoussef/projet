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
            $_SESSION['error'] = 'Failed to set secure session cookie parameters';
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }
        
        if (!session_start()) {
            $_SESSION['error'] = 'Failed to start session';
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                $_SESSION['error'] = 'Failed to generate CSRF token';
                $_SESSION['animation'] = 'windowShake';
                header('Location: /workout/update');
                exit();
            }
        }
    }

    require_once '../BaseController.php';
    require_once '../../models/WorkoutModel.php';
    require_once '../../../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        $_SESSION['error'] = "You must be logged in to update a workout.";
        $_SESSION['animation'] = 'windowShake';
        header('Location: /workout/update');
        exit();
    }

    $workoutModel = new WorkoutModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        // Enhanced CSRF token validation with timing attack protection
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            error_log("CSRF token missing for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
            $_SESSION['error'] = "Security token missing. Please try again.";
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }

        // Use hash_equals for secure comparison
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
            $_SESSION['error'] = "Security token validation failed. Please try again.";
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }

        // Regenerate session ID and CSRF token for security
        if (!session_regenerate_id(true)) {
            $_SESSION['error'] = 'Failed to regenerate session ID';
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Validate and sanitize input
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $data = [
            'exercise_id' => filter_input(INPUT_POST, 'exercise_id', FILTER_VALIDATE_INT),
            'date' => htmlspecialchars($_POST['date'] ?? ''),
            'sets' => filter_input(INPUT_POST, 'sets', FILTER_VALIDATE_INT),
            'reps' => filter_input(INPUT_POST, 'reps', FILTER_VALIDATE_INT),
            'weight' => filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT),
            'user_id' => $_SESSION['user_id']
        ];

        // Validate required fields
        if (!$id || !$data['exercise_id'] || empty($data['date']) || !$data['sets'] || !$data['reps'] || $data['weight'] === false) {
            $_SESSION['error'] = 'Invalid input data. Please check all fields.';
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }

        if (!$workoutModel->updateWorkout($id, $data, $_SESSION['user_id'])) {
            $_SESSION['error'] = 'Failed to update workout data.';
            $_SESSION['animation'] = 'windowShake';
            header('Location: /workout/update');
            exit();
        }

        $_SESSION['success'] = 'Workout updated successfully!';
        $_SESSION['animation'] = 'fadeIn';
        header('Location: /workout/update');
        exit();
    }
} catch (Exception $e) {
    error_log("Error in UpdateController: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    $_SESSION['animation'] = 'windowShake';
    header('Location: /workout/update');
    exit();
}
?>