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
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /user');
    exit();
}

$userModel = new UserModel($pdo);

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

        // Regenerate CSRF token after successful validation
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Regenerate session ID for security on POST requests
        session_regenerate_id(true);

        // Get and validate input data
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('Invalid user ID.');
        }

        if ($userModel->deleteUser($id)) {
            header('Location: /user');
            exit();
        } else {
            throw new Exception('Failed to delete user. The user might not exist or you might not have sufficient permissions.');
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header('Location: /user');
        exit();
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        header('Location: /user');
        exit();
    }
} else {
    header('Location: /user');
    exit();
}