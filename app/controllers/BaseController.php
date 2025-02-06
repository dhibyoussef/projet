<?php
// Secure session configuration with error handling
try {
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true, // secure flag - only send over HTTPS
            true  // httponly flag - prevent JavaScript access
        )) {
            throw new Exception('Failed to set secure session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
    }
} catch (Exception $e) {
    error_log("Session initialization error: " . $e->getMessage());
    http_response_code(500);
    $_SESSION['error_message'] = $e->getMessage();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'animation' => 'slideInDown',
        'code' => 500
    ]);
    exit();
}

// Regenerate session ID periodically with error handling
try {
    if (!isset($_SESSION['last_regeneration'])) {
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 1800) { // 30 minutes
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['last_regeneration'] = time();
    }
} catch (Exception $e) {
    error_log("Session regeneration error: " . $e->getMessage());
    http_response_code(500);
    $_SESSION['error_message'] = $e->getMessage();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'animation' => 'slideInDown',
        'code' => 500
    ]);
    exit();
}

class BaseController {
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;

        try {
            // Enhanced authentication check
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('Authentication required');
            }

            // Initialize CSRF token management
            $this->initializeCsrfProtection();
        } catch (Exception $e) {
            $this->handleError($e->getMessage(), 401);
        }
    }

    protected function initializeCsrfProtection() {
        try {
            // Generate CSRF token if it doesn't exist
            if (!isset($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                if (!$_SESSION['csrf_token']) {
                    throw new Exception('Failed to generate CSRF token');
                }
            }

            // Validate CSRF token for POST requests
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->validateCsrfRequest();
            }
        } catch (Exception $e) {
            $this->handleError($e->getMessage(), 500);
        }
    }

    protected function validateCsrfRequest() {
        try {
            if (!isset($_POST['csrf_token'])) {
                throw new Exception('CSRF token missing');
            }

            if (!$this->validateCsrfToken($_POST['csrf_token'])) {
                throw new Exception('CSRF token validation failed');
            }

            // Regenerate CSRF token after successful validation
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to regenerate CSRF token');
            }
        } catch (Exception $e) {
            $this->handleError($e->getMessage(), 403);
        }
    }

    protected function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    protected function handleError($message, $code = 500) {
        error_log("Error: " . $message . " for user ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        $_SESSION['error_message'] = $message;
        http_response_code($code);
        
        // Return JSON response with animated error display
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'animation' => 'slideInDown',
            'code' => $code,
            'display' => 'inline-block'
        ]);
        exit();
    }

    protected function secureSessionDestroy() {
        try {
            // Clear session data
            $_SESSION = array();
            
            // Delete session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                if (!setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                )) {
                    throw new Exception('Failed to delete session cookie');
                }
            }
            
            // Destroy session
            if (!session_destroy()) {
                throw new Exception('Failed to destroy session');
            }
        } catch (Exception $e) {
            error_log("Session destruction error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'animation' => 'slideInDown',
                'code' => 500
            ]);
            exit();
        }
    }
}
?>