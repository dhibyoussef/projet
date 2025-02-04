<?php
require_once '../BaseController.php';
require_once '../../models/WorkoutModel.php';
require_once '../../../config/database.php';

$workoutModel = new WorkoutModel($pdo);

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $data = [
        'exercise_id' => $_POST['exercise_id'],
        'date' => $_POST['date'],
        'sets' => $_POST['sets'],
        'reps' => $_POST['reps'],
        'weight' => $_POST['weight']
    ];

    if ($workoutModel->updateWorkout($id, $data)) {
        // Redirect or display success message
        header('Location:../../controllers/workout/ReadController.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to update workout data.';
    }
}
?>