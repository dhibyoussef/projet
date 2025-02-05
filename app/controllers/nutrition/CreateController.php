<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session cookie parameters before starting session
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params(
        $sessionParams["lifetime"],
        $sessionParams["path"],
        $sessionParams["domain"],
        true, // secure
        true  // httponly
    );
    session_start();
}

require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to add nutrition data.";
    header('Location: ../../views/user/login.php');
    exit();
}

$nutritionModel = new NutritionModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    // Validate CSRF token
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        header('Location: ../../views/errors/403.php');
        exit();
    }

    // Regenerate session ID for security on POST requests
    session_regenerate_id(true);

    // Get and sanitize input data
    $user_id = $_SESSION['user_id'];
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

    try {
        if ($nutritionModel->addNutrition($data)) {
            $_SESSION['success_message'] = 'Nutrition data added successfully!';
            header('Location: ../../controllers/nutrition/ReadController.php');
            exit();
        } else {
            throw new Exception('Failed to add nutrition data.');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../../views/nutrition/create.php');
        exit();
    }
}
?>