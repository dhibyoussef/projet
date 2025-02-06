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
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    logError("Unauthorized access attempt to DeleteController");
    $_SESSION['error_message'] = "You must be logged in to view progress.";
    echo "<script>window.location.href = '/login';</script>";
    exit();
}

$progressModel = new ProgressModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Validate CSRF token presence and match using hash_equals for timing attack protection
    if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        // Log the CSRF token validation failure with detailed context
        logError("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
                " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
                " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
        
        // Clear the invalid CSRF token
        unset($_SESSION['csrf_token']);
        
        $_SESSION['error_message'] = "Security token validation failed. Please try again.";
        echo "<script>window.location.href = '../../views/errors/403.php';</script>";
        exit();
    }

    // Regenerate session ID and CSRF token for security on POST requests
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Get and validate input data
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        logError("Invalid progress entry ID provided by user ID: " . $_SESSION['user_id']);
        $_SESSION['error_message'] = 'Invalid progress entry ID.';
        echo "<script>window.location.href = '../../controllers/progress/ReadController.php';</script>";
        exit();
    }

    try {
        if ($progressModel->deleteProgress($id, $_SESSION['user_id'])) {
            $_SESSION['success_message'] = 'Progress entry deleted successfully!';
            echo "<script>window.location.href = '../../controllers/progress/ReadController.php';</script>";
            exit();
        } else {
            $errorMsg = 'Failed to delete progress entry. ID: ' . $id . ', User ID: ' . $_SESSION['user_id'];
            logError($errorMsg);
            throw new Exception($errorMsg);
        }
    } catch (PDOException $e) {
        logError("Database error in DeleteController: " . $e->getMessage());
        $_SESSION['error_message'] = 'A database error occurred. Please try again.';
        echo "<script>window.location.href = '../../controllers/progress/ReadController.php';</script>";
        exit();
    } catch (Exception $e) {
        logError("Error in DeleteController: " . $e->getMessage());
        $_SESSION['error_message'] = $e->getMessage();
        echo "<script>window.location.href = '../../controllers/progress/ReadController.php';</script>";
        exit();
    }
} else {
    logError("Invalid request method to DeleteController. Method: " . $_SERVER['REQUEST_METHOD']);
    $_SESSION['error_message'] = 'Invalid request method.';
    echo "<script>window.location.href = '../../controllers/progress/ReadController.php';</script>";
    exit();
}
?>