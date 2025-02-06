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
    $_SESSION['error_message'] = 'You must be logged in to view nutrition.';
    $_SESSION['error_animation'] = 'shake';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

$nutritionModel = new NutritionModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    // Validate CSRF token presence and match
    if (empty($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $_SESSION['error_message'] = 'Security token validation failed. Please try again.';
        $_SESSION['error_animation'] = 'shake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Regenerate session ID and CSRF token for security
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Validate and sanitize input data
    try {
        $user_id = $_SESSION['user_id'];
        $date = htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8');
        $food_item = htmlspecialchars($_POST['food_item'], ENT_QUOTES, 'UTF-8');
        $calories = filter_var($_POST['calories'], FILTER_VALIDATE_INT);
        $protein = filter_var($_POST['protein'], FILTER_VALIDATE_FLOAT);
        $carbs = filter_var($_POST['carbs'], FILTER_VALIDATE_FLOAT);
        $fats = filter_var($_POST['fats'], FILTER_VALIDATE_FLOAT);

        if ($calories === false || $protein === false || $carbs === false || $fats === false) {
            throw new InvalidArgumentException('Invalid nutrition data format');
        }

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
            $_SESSION['success_message'] = 'Nutrition data added successfully!';
            $_SESSION['success_animation'] = 'fadeIn';
            header('Location: ../../controllers/nutrition/ReadController.php');
            exit();
        } else {
            throw new Exception('Failed to add nutrition data to database');
        }
    } catch (InvalidArgumentException $e) {
        $_SESSION['error_message'] = 'Invalid input data. Please check your entries.';
        $_SESSION['error_animation'] = 'shake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'A database error occurred. Please try again later.';
        $_SESSION['error_animation'] = 'shake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'An unexpected error occurred. Please try again.';
        $_SESSION['error_animation'] = 'shake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>