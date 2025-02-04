<?php
require_once '../BaseController.php';
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

$workoutModel = new WorkoutModel($pdo);

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    if ($workoutModel->deleteWorkout($id)) {
        // Redirect or display success message
        header('Location:../../controllers/workout/ReadController.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to delete workout.';
    }
}
?>