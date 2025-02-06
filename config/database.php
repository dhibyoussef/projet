<?php

// Database configuration
$host = 'localhost';
$db = 'fitness_tracker';
$user = 'root'; // Change as needed
$pass = ''; // Change as needed
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Initialize session with secure settings
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
            $_SESSION['error'] = 'Failed to set secure session parameters';
            showError($_SESSION['error']);
            exit();
        }
        
        if (!session_start()) {
            $_SESSION['error'] = 'Failed to start session';
            showError($_SESSION['error']);
            exit();
        }
    }
    
    // Generate CSRF token with expiration
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
        try {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expire'] = time() + 3600; // 1 hour expiration
        } catch (Exception $e) {
            $_SESSION['error'] = 'Failed to generate CSRF token';
            showError($_SESSION['error']);
            exit();
        }
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Database connection failed. Please try again later.';
    showError($_SESSION['error']);
    exit();
} catch (Exception $e) {
    $_SESSION['error'] = 'An unexpected error occurred. Please try again later.';
    showError($_SESSION['error']);
    exit();
}

// Error handling with animated window
function showError($message) {
    echo '<style>
        .error-window {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #dc3545;
            border-radius: 5px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; transform: translate(-50%, -40%); }
            100% { opacity: 1; transform: translate(-50%, -50%); }
        }
    </style>';
    
    echo '<div class="error-window">';
    echo '<div class="alert alert-danger alert-dismissible" role="alert">';
    echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<button type="button" class="close" onclick="this.parentElement.parentElement.remove()">';
    echo '<span aria-hidden="true">&times;</span>';
    echo '</button>';
    echo '</div>';
    echo '</div>';
}

if (isset($_SESSION['error'])) {
    showError($_SESSION['error']);
    unset($_SESSION['error']);
}
?>