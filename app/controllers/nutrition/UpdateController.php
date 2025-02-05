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
    $_SESSION['error_message'] = "You must be logged in to update nutrition data.";
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

    // Get and validate input data
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['error_message'] = 'Invalid nutrition entry ID.';
        header('Location: ../../controllers/nutrition/ReadController.php');
        exit();
    }

    $data = [
        'date' => htmlspecialchars($_POST['date']),
        'food_item' => htmlspecialchars($_POST['food_item']),
        'calories' => htmlspecialchars($_POST['calories']),
        'protein' => htmlspecialchars($_POST['protein']),
        'carbs' => htmlspecialchars($_POST['carbs']),
        'fats' => htmlspecialchars($_POST['fats'])
    ];

    // Validate all required fields
    if (in_array(false, $data, true)) {
        $_SESSION['error_message'] = 'Invalid input data. Please check your values.';
        header('Location: ../../controllers/nutrition/ReadController.php');
        exit();
    }

    try {
        if ($nutritionModel->updateNutrition($id, $data)) {
            $_SESSION['success_message'] = 'Nutrition data updated successfully!';
            header('Location: ../../controllers/nutrition/ReadController.php');
            exit();
        } else {
            throw new Exception('Failed to update nutrition data.');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../../controllers/nutrition/ReadController.php');
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: ../../controllers/nutrition/ReadController.php');
    exit();
}
?>