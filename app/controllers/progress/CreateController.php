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

header('Content-Type: application/json');

try {
    require_once '../BaseController.php';
    require_once '../../models/ProgressModel.php';
    require_once '../../../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        throw new Exception("You must be logged in to view progress.");
    }

    $progressModel = new ProgressModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
        // Validate CSRF token using hash_equals for timing attack protection
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("CSRF token validation failed.");
        }

        // Regenerate session ID and CSRF token for security on POST requests
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Validate and sanitize input data
        if (empty($_POST['date']) || empty($_POST['weight']) || empty($_POST['body_fat'])) {
            throw new Exception("All fields are required.");
        }

        $user_id = $_SESSION['user_id'];
        $date = htmlspecialchars($_POST['date'], ENT_QUOTES, 'UTF-8');
        $weight = filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $body_fat = filter_input(INPUT_POST, 'body_fat', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
        if (!is_numeric($weight) || !is_numeric($body_fat)) {
            throw new Exception("Weight and body fat must be numeric values.");
        }

        $data = [
            'user_id' => $user_id,
            'date' => $date,
            'weight' => $weight,
            'body_fat_percentage' => $body_fat
        ];

        if (!$progressModel->createProgress($data)) {
            throw new Exception('Failed to add progress data.');
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Progress data added successfully!',
            'animation' => 'fadeIn',
            'redirect' => false
        ]);
        exit();
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'animation' => 'window',
        'redirect' => false
    ]);
    exit();
}
?>