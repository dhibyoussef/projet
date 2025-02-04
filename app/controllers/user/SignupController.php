<?php
require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

try {
    $userModel = new UserModel($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validate password strength
        if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            echo "Password must be at least 8 characters long, contain at least one uppercase letter and one number.";
        } elseif ($userModel->emailExists($email)) {
            // Handle email already exists error
            echo "Email already exists.";
        } else {
            $userModel->createUser($name, $email, $password);
            header('Location: ../../views/user/profile.php');
            exit();
        }
    }

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}
?>