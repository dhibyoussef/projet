<?php
require_once '../BaseController.php';
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

$progressModel = new ProgressModel($pdo);

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $progressModel->deleteProgress($id);
    header('Location: ../../views/progress/index.php');
    exit;
} else {
    echo 'Invalid request.';
}
?>