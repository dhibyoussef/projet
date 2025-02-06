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
    
    // Generate CSRF token if it doesn't exist
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

require_once '../BaseController.php';
require_once '../../models/NutritionModel.php';
require_once '../../../config/database.php';
require_once '../../../config/error.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    $_SESSION['error'] = 'You must be logged in to view nutrition.';
    header('Location: /nutrition');
    exit();
}

$nutritionModel = new NutritionModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    // Enhanced CSRF token validation
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
        $_SESSION['error'] = 'Security token missing. Please try again.';
        header('Location: /nutrition');
        exit();
    }

    // Use hash_equals for timing attack safe comparison
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error'] = 'Security token validation failed. Please try again.';
        header('Location: /nutrition');
        exit();
    }

    // Regenerate CSRF token after successful validation
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Get and validate input data
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['error'] = 'Invalid nutrition entry ID.';
        header('Location: /nutrition');
        exit();
    }

    // Validate and sanitize input data
    try {
        $data = [
            'date' => htmlspecialchars($_POST['date']),
            'food_item' => htmlspecialchars($_POST['food_item']),
            'calories' => filter_input(INPUT_POST, 'calories', FILTER_VALIDATE_FLOAT),
            'protein' => filter_input(INPUT_POST, 'protein', FILTER_VALIDATE_FLOAT),
            'carbs' => filter_input(INPUT_POST, 'carbs', FILTER_VALIDATE_FLOAT),
            'fats' => filter_input(INPUT_POST, 'fats', FILTER_VALIDATE_FLOAT)
        ];

        // Validate all required fields
        if (in_array(false, $data, true)) {
            $_SESSION['error'] = 'Invalid input data. Please check your values.';
            header('Location: /nutrition');
            exit();
        }

        if ($nutritionModel->updateNutrition($id, $data)) {
            $_SESSION['success'] = 'Nutrition data updated successfully!';
            header('Location: /nutrition');
            exit();
        } else {
            throw new Exception('Failed to update nutrition data.');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'An error occurred while updating nutrition data. Please try again.';
        header('Location: /nutrition');
        exit();
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
    header('Location: /nutrition');
    exit();
}
?>