<?php
require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';

$nutritionModel = new NutritionModel($pdo);


$nutritionLogs = $nutritionModel->getNutrition();


include '../../views/nutrition/index.php';
?>