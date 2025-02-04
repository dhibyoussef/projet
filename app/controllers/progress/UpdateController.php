<?php
require_once '../BaseController.php';
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

$progressModel = new ProgressModel($pdo);

if (isset($_POST['ok'])) {
    $id = $_POST['id'];
    $data = [
        'date' => $_POST['date'],
        'weight' => $_POST['weight'],
        'body_fat_percentage' => $_POST['body_fat_percentage']
    ];

    if ($progressModel->updateProgress($id, $data)) {
        // Redirect or display success message
        header('Location:../../controllers/progress/ReadController.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to update progress data.';
    }
}
?>