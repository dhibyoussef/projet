<?php
header('Content-Type: application/json');

try {
    // Check if session is already started
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session cookie parameters before starting session
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true, // secure
            true  // httponly
        )) {
            throw new Exception('Failed to set session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }

    require_once '../BaseController.php';
    require_once '../../models/UserModel.php';
    require_once '../../../config/database.php';

    // Validate CSRF token for POST requests using hash_equals for timing attack protection
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Log the CSRF token validation failure with detailed context
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
                     " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
                     " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
            
            // Clear the invalid CSRF token
            unset($_SESSION['csrf_token']);
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Security token validation failed. Please try again.',
                'animation' => 'window-shake',
                'stayOnPage' => true
            ]);
            exit();
        }
        // Regenerate session ID for security on POST requests
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
    }

    if (isset($_SESSION['user_id'])) {
        // Unset all session variables
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            if (!setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            )) {
                throw new Exception('Failed to delete session cookie');
            }
        }

        // Destroy the session
        if (!session_destroy()) {
            throw new Exception('Failed to destroy session');
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'You have been successfully logged out.',
            'animation' => 'window-fade',
            'redirect' => '../../index.php'
        ]);
        exit();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'You must be logged in to log out.',
            'animation' => 'window-shake',
            'stayOnPage' => true
        ]);
        exit();
    }
} catch (Exception $e) {
    // Log the error with additional context
    error_log('LogoutController Error: ' . $e->getMessage() . 
             ' | User ID: ' . ($_SESSION['user_id'] ?? 'unknown') . 
             ' | IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'animation' => 'window-shake',
        'stayOnPage' => true
    ]);
    exit();
}
?>