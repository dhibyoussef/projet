<?php
function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log';
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}
?>