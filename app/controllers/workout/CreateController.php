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
    $_SESSION['error_message'] = "You must be logged in to create a workout.";
    header('Location: ../../views/user/login.php');
    exit();
}

$workoutModel = new WorkoutModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    // Validate CSRF token
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/error.php');
        exit();
    }

    // Regenerate session ID for security
    session_regenerate_id(true);

    // Validate and sanitize input
    $workoutName = htmlspecialchars($_POST['workoutName'] ?? '');
    $workoutDescription = htmlspecialchars($_POST['workoutDescription'] ?? '');
    $workoutDuration = htmlspecialchars($_POST['workoutDuration'] ?? '');

    // Basic input validation
    if (empty($workoutName) || empty($workoutDuration)) {
        $_SESSION['error_message'] = 'Workout name and duration are required.';
        header('Location: ../../views/workout/create.php');
        exit();
    }

    $data = [
        'id' => $_SESSION['user_id'],
        'workout_name' => $workoutName,
        'description' => $workoutDescription,
        'duration' => $workoutDuration
    ];

    if ($workoutModel->createWorkout($data)) {
        $_SESSION['success_message'] = 'Workout created successfully!';
        header('Location: ../../views/workout/index.php');
        exit();
    } else {
        $_SESSION['error_message'] = 'Failed to create workout.';
        header('Location: ../../views/workout/create.php');
        exit();
    }
}

include "../../views/workout/create.php";
?>