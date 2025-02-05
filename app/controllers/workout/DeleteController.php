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
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

$workoutModel = new WorkoutModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Validate session and CSRF token
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/errors/403.php');
        exit();
    }

    // Regenerate session ID for security
    session_regenerate_id(true);

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['error_message'] = 'Invalid workout ID.';
        header('Location: ../../views/workout/index.php');
        exit();
    }

    if ($workoutModel->deleteWorkout($id)) {
        // Set success message in session
        $_SESSION['success_message'] = 'Workout deleted successfully!';
        header('Location: ../../views/workout/index.php');
        exit();
    } else {
        // Set error message in session
        $_SESSION['error_message'] = 'Failed to delete workout.';
        header('Location: ../../views/workout/index.php');
        exit();
    }
}
?>