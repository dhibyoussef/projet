<?php
function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0755, true);
    }
    
    // Add error context information
    $context = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'
    ];
    
    // Convert context to JSON for structured logging
    $logEntry = json_encode($context, JSON_PRETTY_PRINT) . PHP_EOL;
    
    // Write to log file with error handling
    try {
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
        // Fallback to default PHP error logging if file write fails
        error_log('Failed to write to error log: ' . $e->getMessage());
        error_log($message);
    }
}
?>