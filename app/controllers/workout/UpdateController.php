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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to update a workout.";
    header('Location: ../../views/user/login.php');
    exit();
}

$workoutModel = new WorkoutModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Validate CSRF token
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/error.php');
        exit();
    }

    // Regenerate session ID for security
    session_regenerate_id(true);

    // Validate and sanitize input
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $data = [
        'exercise_id' => filter_input(INPUT_POST, 'exercise_id', FILTER_VALIDATE_INT),
        'date' => htmlspecialchars($_POST['date']),
        'sets' => filter_input(INPUT_POST, 'sets', FILTER_VALIDATE_INT),
        'reps' => filter_input(INPUT_POST, 'reps', FILTER_VALIDATE_INT),
        'weight' => filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_FLOAT)
    ];

    // Validate required fields
    if (!$id || !$data['exercise_id'] || empty($data['date']) || !$data['sets'] || !$data['reps'] || $data['weight'] === false) {
        $_SESSION['error_message'] = 'Invalid input data. Please check all fields.';
        header('Location: ../../views/workout/index.php');
        exit();
    }

    try {
        if ($workoutModel->updateWorkout($id, $data)) {
            $_SESSION['success_message'] = 'Workout updated successfully!';
            header('Location: ../../views/workout/index.php');
            exit();
        } else {
            throw new Exception('Failed to update workout data.');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../../views/workout/index.php');
        exit();
    }
}
?>