<?php
require_once '../BaseController.php';
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

$workoutModel = new WorkoutModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    $workoutName = htmlspecialchars($_POST['workoutName']);
    $workoutDescription = htmlspecialchars($_POST['workoutDescription']);
    $workoutDuration = htmlspecialchars($_POST['workoutDuration']);
    $data = [
        'id' => $user_id,
        'workout_name' => $workoutName,
        'description' => $workoutDescription,
        'duration' => $workoutDuration
    ];
    var_dump($data);

    if ($workoutModel->createWorkout($data)) {
        header('Location:../../controllers/workout/ReadController.php');
        exit;
    } else {
        echo 'Failed to create workout.';
    }
}
include "../../views/workout/create.php";
?>