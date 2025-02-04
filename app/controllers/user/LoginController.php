<?php
require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

$userModel = new UserModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $userModel->authenticate($email, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: ../../views/user/profile.php');
        exit();
    } else {
        // Handle login error
        echo "Invalid email or password.";
    }
}
?>