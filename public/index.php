<?php
// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    $sessionParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => $sessionParams['lifetime'],
        'path' => '/',
        'domain' => $sessionParams['domain'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Generate CSRF token with expiration
if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expire'] = time() + 3600; // 1 hour expiration
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Failed to generate CSRF token';
    }
}

// Include necessary files
require_once '../config/database.php';
require_once '../config/error.php';
require_once '../app/controllers/ErrorController.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/ExerciseController.php';
require_once '../app/controllers/NutritionController.php';
require_once '../app/controllers/ProgressController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/WorkoutController.php';

// Check if ErrorController exists
if (!class_exists('ErrorController')) {
    $_SESSION['error_message'] = 'System configuration error';
    header('Location: /');
    exit();
}

// Create PDO instance
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $_SESSION['error_message'] = 'Database connection failed. Please try again later.';
    header('Location: /');
    exit();
}

// Routing logic
$controller = 'HomeController';
$action = 'index';

if (isset($_GET['controller']) && isset($_GET['action'])) {
    $controller = ucfirst($_GET['controller']) . 'Controller';
    $action = $_GET['action'];
}

// Protected routes check
$protectedControllers = ['ExerciseController', 'NutritionController', 'ProgressController', 'WorkoutController'];
if (in_array($controller, $protectedControllers) && !isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = 'Please login to access this page';
    header('Location: /?controller=User&action=login');
    exit();
}

// CSRF validation for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error_message'] = 'Security token validation failed. Please try again.';
        // Instead of redirecting, show error in current page
        echo '<div class="error-window animate__animated animate__slideInDown" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 20px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 10000;">';
        echo '<div class="error-content">';
        echo '<h4 style="margin: 0 0 10px; color: #c62828;">Security Error</h4>';
        echo '<p style="margin: 0;">Security token validation failed. Please try again.</p>';
        echo '<button onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer; font-size: 16px;">×</button>';
        echo '</div></div>';
        exit();
    }
}

// Controller instantiation and action execution
if (class_exists($controller)) {
    try {
        $controllerInstance = new $controller($pdo);
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            $_SESSION['error_message'] = 'Requested action not found';
            // Show error in current page
            echo '<div class="error-window animate__animated animate__slideInDown" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 20px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 10000;">';
            echo '<div class="error-content">';
            echo '<h4 style="margin: 0 0 10px; color: #c62828;">Action Not Found</h4>';
            echo '<p style="margin: 0;">The requested action could not be found.</p>';
            echo '<button onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer; font-size: 16px;">×</button>';
            echo '</div></div>';
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'An unexpected error occurred. Please try again.';
        // Show error in current page
        echo '<div class="error-window animate__animated animate__slideInDown" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 20px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 10000;">';
        echo '<div class="error-content">';
        echo '<h4 style="margin: 0 0 10px; color: #c62828;">Unexpected Error</h4>';
        echo '<p style="margin: 0;">An unexpected error occurred. Please try again.</p>';
        echo '<button onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer; font-size: 16px;">×</button>';
        echo '</div></div>';
        exit();
    }
} else {
    $_SESSION['error_message'] = 'Requested page not found';
    // Show error in current page
    echo '<div class="error-window animate__animated animate__slideInDown" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 20px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 10000;">';
    echo '<div class="error-content">';
    echo '<h4 style="margin: 0 0 10px; color: #c62828;">Page Not Found</h4>';
    echo '<p style="margin: 0;">The requested page could not be found.</p>';
    echo '<button onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer; font-size: 16px;">×</button>';
    echo '</div></div>';
    exit();
}

// Display error message if exists
if (isset($_SESSION['error_message'])) {
    echo '<div class="error-window animate__animated animate__slideInDown" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); padding: 20px; background: #ffebee; border: 1px solid #ffcdd2; border-radius: 4px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); z-index: 10000;">';
    echo '<div class="error-content">';
    echo '<h4 style="margin: 0 0 10px; color: #c62828;">Error</h4>';
    echo '<p style="margin: 0;">' . htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<button onclick="this.parentElement.parentElement.remove()" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer; font-size: 16px;">×</button>';
    echo '</div></div>';
    unset($_SESSION['error_message']);
}
?>