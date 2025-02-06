<?php
try {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new Exception('Failed to set session cookie parameters');
        }
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
    }

    // Verify CSRF token if present
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Invalid CSRF token');
        }
    }

    // Store logout message before clearing session
    $message = 'You have been successfully logged out.';
    $message_type = 'success';

    // Regenerate session ID for security
    if (!session_regenerate_id(true)) {
        throw new Exception('Failed to regenerate session ID');
    }

    // Clear session data
    $_SESSION = [];

    // If it's desired to kill the session, also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        if (!setcookie(
            session_name(), 
            '', 
            time() - 42000,
            $params["path"], 
            $params["domain"], 
            $params["secure"], 
            $params["httponly"]
        )) {
            throw new Exception('Failed to delete session cookie');
        }
    }

    // Destroy the session
    if (!session_destroy()) {
        throw new Exception('Failed to destroy session');
    }

    // Start new session for the message with secure settings
    if (!session_start()) {
        throw new Exception('Failed to start new session');
    }
    if (!session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $params["domain"],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ])) {
        throw new Exception('Failed to set new session cookie parameters');
    }

    // Generate new CSRF token for the next session
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    if (!$_SESSION['csrf_token']) {
        throw new Exception('Failed to generate CSRF token');
    }
    $_SESSION['csrf_token_expire'] = time() + 3600; // Token expires in 1 hour

    // Output message with animation
    echo '<div id="logout-message" class="message-animation">'.$message.'</div>';
    echo '<style>
        .message-animation {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            animation: slideIn 0.5s ease-out;
            z-index: 1000;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>';
    echo '<script>
        setTimeout(function() {
            var message = document.getElementById("logout-message");
            if (message) {
                message.style.animation = "slideOut 0.5s ease-out";
                setTimeout(function() {
                    window.location.href = "/login";
                }, 500);
            }
        }, 3000);
    </script>';

} catch (Exception $e) {
    // Log the error and show error message in the same page with window animation
    error_log('Logout Error: ' . $e->getMessage());
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    echo '<div id="error-window" class="window-animation">
            <div class="window-header">Error</div>
            <div class="window-content">An error occurred during logout. Please try again.</div>
            <button onclick="closeErrorWindow()" class="window-button">OK</button>
          </div>';
    echo '<style>
        .window-animation {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            width: 300px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            animation: windowOpen 0.3s ease-out forwards;
            z-index: 1000;
        }
        .window-header {
            padding: 15px;
            background-color: #f44336;
            color: white;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
        }
        .window-content {
            padding: 20px;
            color: #333;
        }
        .window-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 0 0 8px 8px;
            cursor: pointer;
        }
        .window-button:hover {
            background-color: #e53935;
        }
        @keyframes windowOpen {
            to { transform: translate(-50%, -50%) scale(1); }
        }
    </style>';
    echo '<script>
        function closeErrorWindow() {
            var window = document.getElementById("error-window");
            if (window) {
                window.style.animation = "windowClose 0.3s ease-out forwards";
                setTimeout(function() {
                    window.remove();
                }, 300);
            }
        }
        @keyframes windowClose {
            from { transform: translate(-50%, -50%) scale(1); }
            to { transform: translate(-50%, -50%) scale(0); }
        }
    </script>';
}
?>