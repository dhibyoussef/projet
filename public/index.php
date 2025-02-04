<?php
session_start(); // Start the session

// Include necessary files
require_once '../config/database.php'; // Database connection
require_once '../app/controllers/ErrorController.php';
require_once '../app/controllers/HomeController.php';
require_once '../app/controllers/ExerciseController.php';
require_once '../app/controllers/NutritionController.php';
require_once '../app/controllers/ProgressController.php';
require_once '../app/controllers/UserController.php';
require_once '../app/controllers/WorkoutController.php';

// Check if ErrorController class exists
if (!class_exists('ErrorController')) {
    die("Error: 'ErrorController' class not found.");
}

// Create a new PDO instance
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, show login and signup options
    include '../index.php'; // Render the home view with login/signup options
    exit();
}

// Simple routing logic
$controller = 'HomeController'; // Default controller
$action = 'index'; // Default action

// Check if a controller and action are specified in the URL
if (isset($_GET['controller']) && isset($_GET['action'])) {
    $controller = ucfirst($_GET['controller']) . 'Controller';
    $action = $_GET['action'];
}

// Instantiate the controller
if (class_exists($controller)) {
    $controllerInstance = new $controller($pdo);
    if (method_exists($controllerInstance, $action)) {
        $controllerInstance->$action();
    }
}
?>