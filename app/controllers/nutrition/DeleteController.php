<?php
require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';

$nutritionModel = new NutritionModel($pdo);

if (isset($_POST['delete'])) {
    $id = $_POST['id'];

    if ($nutritionModel->deleteNutrition($id)) {
        // Redirect or display success message
        header(header: 'Location:../../controllers/nutrition/ReadController.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to delete nutrition entry.';
    }
} else {
    echo 'Invalid request.';
}
?>