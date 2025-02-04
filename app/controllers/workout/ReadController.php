<?php
require_once '../BaseController.php';
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

$workoutModel = new WorkoutModel($pdo);

$workouts = $workoutModel->getAllWorkouts();

include '../../views/workout/index.php';
?>