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
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        if (!session_start()) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }

    require_once '../BaseController.php';
    require_once '../../models/UserModel.php';
    require_once '../../../config/database.php';

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $userModel = new UserModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
        // Enhanced CSRF token validation with timing attack protection
        if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            error_log("CSRF token validation failed for user ID: " . ($_SESSION['user_id'] ?? 'unknown') . 
                     " | IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . 
                     " | User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Regenerate session ID and CSRF token for security on POST requests
        if (!session_regenerate_id(true)) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        if (!$_SESSION['csrf_token']) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Get and validate input data
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];

        if ($userModel->updateUser($id, $name, $email, $password)) {
            // Update session email if it's the current user
            if ($id == $_SESSION['user_id']) {
                $_SESSION['email'] = $email;
            }
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        } else {
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
    } else {
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
} catch (Exception $e) {
    error_log('ProfileController error: ' . $e->getMessage());
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}
?>