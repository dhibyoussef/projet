<?php
require_once '../BaseController.php';
require_once '../../models/ProgressModel.php';
require_once '../../../config/database.php';

$progressModel = new ProgressModel($pdo);

$progressLogs = $progressModel->getAllProgress();

include '../../views/progress/index.php';
?>