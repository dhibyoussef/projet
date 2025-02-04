<?php
require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

$userModel = new UserModel($pdo);

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($userModel->updateUser($id, $name, $email, $password)) {
        // Redirect or display success message
        header('Location: ../../views/user/profile.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to update user data.';
    }
}
?>