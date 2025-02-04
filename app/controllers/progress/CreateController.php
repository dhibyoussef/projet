<?php
require_once '../BaseController.php';
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

$progressModel = new ProgressModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    // Check if 'user_id' is set in POST data
    $user_id = isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : null;
    $date = htmlspecialchars($_POST['date']);
    $weight = htmlspecialchars($_POST['weight']);
    $body_fat = htmlspecialchars($_POST['body_fat']);
    
    $data = [
        'user_id' => $user_id,
        'date' => $date,
        'weight' => $weight,
        'body_fat_percentage' => $body_fat
    ];

    if ($progressModel->createProgress($data)) {
        // Redirect or display success message
        header('Location:../../controllers/progress/ReadController.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to create progress data.';
    }
}
?>