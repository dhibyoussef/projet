<?php
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
            header('Location: update.php');
            exit();
        }
        
        if (!session_start()) {
            header('Location: update.php');
            exit();
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                header('Location: update.php');
                exit();
            }
        }
    }

    require_once '../BaseController.php';
    require_once '../../models/UserModel.php';
    require_once '../../../config/database.php';

    $userModel = new UserModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        // Enhanced CSRF token validation with timing attack protection
        if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token'])) {
            header('Location: update.php');
            exit();
        }

        if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Log the CSRF token validation failure
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
                     " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
                     " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
            
            // Clear the invalid CSRF token
            unset($_SESSION['csrf_token']);
            
            header('Location: update.php');
            exit();
        }

        // Regenerate session ID and CSRF token for security
        if (!session_regenerate_id(true)) {
            header('Location: update.php');
            exit();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Validate and sanitize input
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = htmlspecialchars(trim($_POST['name'] ?? ''));
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Validate required fields
        if (!$id || empty($name) || empty($email)) {
            header('Location: update.php');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header('Location: update.php');
            exit();
        }

        // Hash the password if it's being updated
        $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

        if ($userModel->updateUser($id, $name, $email, $hashedPassword)) {
            // Update session email if it's the current user
            if ($id == $_SESSION['user_id']) {
                $_SESSION['email'] = $email;
            }
            
            header('Location: update.php');
            exit();
        } else {
            header('Location: update.php');
            exit();
        }
    } else {
        header('Location: update.php');
        exit();
    }
} catch (Exception $e) {
    // Log the error with additional context
    error_log("UpdateController Error: " . $e->getMessage() . 
             " | User ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
             " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
             " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
    
    header('Location: update.php');
    exit();
}
?>