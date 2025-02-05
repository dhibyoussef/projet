<?php
// Secure session configuration
if (session_status() === PHP_SESSION_NONE) {
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params(
        $sessionParams["lifetime"],
        $sessionParams["path"],
        $sessionParams["domain"],
        true, // secure flag - only send over HTTPS
        true  // httponly flag - prevent JavaScript access
    );
    session_start();
}

// Regenerate session ID periodically to prevent session fixation
if (!isset($_SESSION['last_regeneration'])) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

class BaseController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;

        // Enhanced authentication check
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Authentication required';
            header('Location: ../../views/user/login.php');
            exit();
        }

        // CSRF protection for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || !$this->validateCsrfToken($_POST['csrf_token'])) {
                $_SESSION['error_message'] = 'CSRF token validation failed';
                header('Location: ../../views/errors/403.php');
                exit();
            }
        }

        // Generate and manage CSRF token
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    protected function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function secureSessionDestroy() {
        // Clear session data
        $_SESSION = array();
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
    }
}
?>