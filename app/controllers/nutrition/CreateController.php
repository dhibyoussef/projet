<?php
require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';

$nutritionModel = new NutritionModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    // Check if 'user_id' is set in POST data
    $user_id = isset($_POST['user_id']) ? htmlspecialchars($_POST['user_id']) : null;
    $date = htmlspecialchars($_POST['date']);
    $food_item = htmlspecialchars($_POST['food_item']);
    $calories = htmlspecialchars($_POST['calories']);
    $protein = htmlspecialchars($_POST['protein']);
    $carbs = htmlspecialchars($_POST['carbs']);
    $fats = htmlspecialchars($_POST['fats']);
    
    $data = [
        'user_id' => $user_id,
        'date' => $date,
        'food_item' => $food_item,
        'calories' => $calories,
        'protein' => $protein,
        'carbs' => $carbs,
        'fats' => $fats
    ];

    if ($nutritionModel->addNutrition($data)) {
        // Redirect or display success message
        header(header: 'Location:../../controllers/nutrition/ReadController.php');

        exit;
    } else {
        // Display error message
        echo 'Failed to add nutrition data.';
    }
}
?>