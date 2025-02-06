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

require_once '../BaseController.php';
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to view progress.',
        'animation' => 'windowShake'
    ]);
    exit();
}

$progressModel = new ProgressModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ok'])) {
    try {
        // Enhanced CSRF token validation
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            throw new Exception("CSRF token missing.");
        }

        // Use hash_equals for timing attack safe comparison
        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            throw new Exception("CSRF token validation failed.");
        }

        // Regenerate session ID and CSRF token for security on POST requests
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Get and validate input data
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid progress entry ID.');
        }

        $data = [
            'date' => htmlspecialchars($_POST['date']),
            'weight' => filter_input(INPUT_POST, 'weight', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'body_fat_percentage' => filter_input(INPUT_POST, 'body_fat_percentage', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
        ];

        // Validate all required fields
        if (empty($data['date']) || empty($data['weight']) || empty($data['body_fat_percentage'])) {
            throw new Exception('All fields are required.');
        }

        if (!is_numeric($data['weight']) || !is_numeric($data['body_fat_percentage'])) {
            throw new Exception('Weight and body fat percentage must be numeric values.');
        }

        if (!$progressModel->updateProgress($id, $data)) {
            throw new Exception('Failed to update progress data.');
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Progress data updated successfully!',
            'animation' => 'windowFadeIn'
        ]);
        exit();

    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage(),
            'animation' => 'windowShake'
        ]);
        exit();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.',
        'animation' => 'windowShake'
    ]);
    exit();
}
?>