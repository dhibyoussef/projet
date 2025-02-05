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
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to add progress data.";
    header('Location: ../../views/user/login.php');
    exit();
}

$progressModel = new ProgressModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
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
    $weight = htmlspecialchars($_POST['weight']);
    $body_fat = htmlspecialchars($_POST['body_fat']);
    
    $data = [
        'user_id' => $user_id,
        'date' => $date,
        'weight' => $weight,
        'body_fat_percentage' => $body_fat
    ];

    try {
        if ($progressModel->createProgress($data)) {
            $_SESSION['success_message'] = 'Progress data added successfully!';
            header('Location: ../../controllers/progress/ReadController.php');
            exit();
        } else {
            throw new Exception('Failed to add progress data.');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ../../views/progress/create.php');
        exit();
    }
}
?>