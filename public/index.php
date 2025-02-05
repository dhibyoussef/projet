<?php
session_start(); // Start the session

// Include necessary files
require_once '../config/database.php'; // Database connection
require_once '../config/error.php'; // Error handling
require_once '../app/controllers/ErrorController.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/ExerciseController.php';
require_once '../app/controllers/NutritionController.php';
require_once '../app/controllers/ProgressController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/WorkoutController.php';

// Check if ErrorController class exists
if (!class_exists('ErrorController')) {
    logError("ErrorController class not found");
    die("An unexpected error occurred. Please try again later.");
}

// Create a new PDO instance
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    logError("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please try again later.");
}

// Simple routing logic
$controller = 'HomeController'; // Default controller
$action = 'index'; // Default action

// Check if a controller and action are specified in the URL
if (isset($_GET['controller']) && isset($_GET['action'])) {
    $controller = ucfirst($_GET['controller']) . 'Controller';
    $action = $_GET['action'];
}

// Check if the user is logged in for protected routes
$protectedControllers = ['ExerciseController', 'NutritionController', 'ProgressController', 'WorkoutController'];
if (in_array($controller, $protectedControllers) && !isset($_SESSION['user_id'])) {
    header('Location: /?controller=User&action=login');
    exit();
}

// Instantiate the controller
if (class_exists($controller)) {
    try {
        $controllerInstance = new $controller($pdo);
        if (method_exists($controllerInstance, $action)) {
            $controllerInstance->$action();
        } else {
            logError("Action '$action' not found in controller '$controller'");
            header('Location: /error/not-found');
            exit();
        }
    } catch (Exception $e) {
        logError("Controller error: " . $e->getMessage());
        header('Location: /error');
        exit();
    }
} else {
    logError("Controller '$controller' not found");
    header('Location: /error/not-found');
    exit();
}
?>