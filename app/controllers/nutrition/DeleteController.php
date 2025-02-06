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
    
    // Generate CSRF token if not already set
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

try {
    require_once '../BaseController.php';
    require_once '../../models/NutritionModel.php';
    require_once '../../../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        $_SESSION['error_message'] = "You must be logged in to view nutrition.";
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $nutritionModel = new NutritionModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['delete'])) {
        $_SESSION['error_message'] = 'Invalid request method.';
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Validate CSRF token presence and match
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error_message'] = "CSRF token validation failed.";
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Regenerate CSRF token and session ID for security
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Get and validate input data
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        $_SESSION['error_message'] = 'Invalid nutrition entry ID.';
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    if (!$nutritionModel->deleteNutrition($id, $_SESSION['user_id'])) {
        $_SESSION['error_message'] = 'Failed to delete nutrition entry.';
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Set success message and redirect back
    $_SESSION['success_message'] = 'Nutrition entry deleted successfully!';
    $_SESSION['success_animation'] = 'windowFadeIn';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();

} catch (Exception $e) {
    // Log the error
    error_log('DeleteController Error: ' . $e->getMessage());
    
    // Set error message and redirect back
    $_SESSION['error_message'] = $e->getMessage();
    $_SESSION['error_animation'] = 'windowShake';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>