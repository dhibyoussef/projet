<?php
require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';

$nutritionModel = new NutritionModel($pdo);

if (isset($_POST['ok'])) {
    $id = $_POST['id'];
    $data = [
        'date' => $_POST['date'],
        'food_item' => $_POST['food_item'],
        'calories' => $_POST['calories'],
        'protein' => $_POST['protein'],
        'carbs' => $_POST['carbs'],
        'fats' => $_POST['fats']
    ];

    if ($nutritionModel->updateNutrition($id, $data)) {
        // Redirect or display success message
        header(header: 'Location:../../controllers/nutrition/ReadController.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to update nutrition data.';
    }
}
?>