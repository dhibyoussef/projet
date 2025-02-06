<?php
// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    try {
        // Set secure session cookie parameters before starting session
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true, // secure
            true  // httponly
        )) {
            $_SESSION['error_message'] = 'Failed to set session cookie parameters';
            $_SESSION['error_animation'] = 'windowShake';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        if (!session_start()) {
            $_SESSION['error_message'] = 'Failed to start session';
            $_SESSION['error_animation'] = 'windowShake';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                $_SESSION['error_message'] = 'Failed to generate CSRF token';
                $_SESSION['error_animation'] = 'windowShake';
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    } catch (Exception $e) {
        error_log('Session initialization error: ' . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

try {
    require_once '../BaseController.php';
    require_once '../../models/ProgressModel.php';
    require_once '../../../config/database.php';
    
    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        $_SESSION['error_message'] = 'You must be logged in to view progress.';
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $progressModel = new ProgressModel($pdo);

    // Validate CSRF token for POST requests using secure comparison
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $_SESSION['error_message'] = 'CSRF token validation failed.';
            $_SESSION['error_animation'] = 'windowShake';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        // Regenerate session ID and CSRF token for security on POST requests
        if (!session_regenerate_id(true)) {
            $_SESSION['error_message'] = 'Failed to regenerate session ID';
            $_SESSION['error_animation'] = 'windowShake';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        if (!$_SESSION['csrf_token']) {
            $_SESSION['error_message'] = 'Failed to regenerate CSRF token';
            $_SESSION['error_animation'] = 'windowShake';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }

    // Get progress logs for the logged-in user
    $progressLogs = $progressModel->getAllProgress();
    if ($progressLogs === false) {
        $_SESSION['error_message'] = 'Failed to retrieve progress data';
        $_SESSION['error_animation'] = 'windowShake';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Store data in session for the view
    $_SESSION['progress_data'] = $progressLogs;
    
    // Redirect back to the same page with data
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();

} catch (Exception $e) {
    error_log('Controller error: ' . $e->getMessage());
    $_SESSION['error_message'] = $e->getMessage();
    $_SESSION['error_animation'] = 'windowShake';
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>