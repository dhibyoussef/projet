<?php
require_once '../BaseController.php';
require_once '../../models/UserModel.php';
require_once '../../../config/database.php';

$userModel = new UserModel($pdo);

if (isset($_POST['ok'])) {
    $id = $_POST['id'];

    if ($userModel->deleteUser($id)) {
        // Redirect or display success message
        header('Location:../../views/user/signup.php');
        exit;
    } else {
        // Display error message
        echo 'Failed to delete user.';
    }
}