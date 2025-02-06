<?php
if (!function_exists('showError')) {
    function showError($message) {
        // Store error message in session for display
        $_SESSION['error_message'] = $message;
        
        // Instead of redirecting, we'll display the error on the same page
        // The error will be handled by the JavaScript and CSS below
    }
}

function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir(dirname($logFile))) {
        if (!mkdir(dirname($logFile), 0755, true)) {
            showError('Failed to create logs directory');
            return false;
        }
    }
    
    // Add error context information
    $context = [
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => $message,
        'request_uri' => $_SERVER['REQUEST_URI'] ?? 'N/A',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
        'csrf_token' => $_SESSION['csrf_token'] ?? 'N/A'
    ];
    
    // Convert context to JSON for structured logging
    $logEntry = json_encode($context, JSON_PRETTY_PRINT) . PHP_EOL;
    if ($logEntry === false) {
        showError('Failed to encode error context to JSON');
        $logEntry = "Error: Failed to encode context\n" . print_r($context, true) . "\n";
    }
    
    // Write to log file with error handling
    try {
        $result = file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        if ($result === false) {
            throw new Exception('Failed to write to error log file');
        }
        return true;
    } catch (Exception $e) {
        showError('Error logging failure: ' . $e->getMessage());
        showError('Original error message: ' . $message);
        return false;
    }
}

if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                showError('Failed to generate CSRF token: ' . $e->getMessage());
                return false;
            }
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCsrfToken')) {
    function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            showError('CSRF token not found in session');
            return false;
        }
        
        if (!is_string($token)) {
            showError('Invalid CSRF token format');
            return false;
        }
        
        if (!hash_equals($_SESSION['csrf_token'], $token)) {
            showError('CSRF token validation failed');
            return false;
        }
        
        return true;
    }
}

// Add this to your HTML template where you want errors to appear:
/*
<div id="error-window" class="error-window animate__animated" style="display: none;">
    <div class="error-content">
        <h3>Error</h3>
        <?php if (isset($_SESSION['error_message'])): ?>
<p><?= htmlspecialchars($_SESSION['error_message']) ?></p>
<?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
<button onclick="closeErrorWindow()" class="btn btn-primary">OK</button>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const errorWindow = document.getElementById('error-window');
    if (errorWindow.innerHTML.includes('Error')) {
        errorWindow.style.display = 'block';
        errorWindow.classList.add('animate__fadeIn');

        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorWindow.classList.remove('animate__fadeIn');
            errorWindow.classList.add('animate__fadeOut');
            setTimeout(() => {
                errorWindow.style.display = 'none';
            }, 500);
        }, 5000);
    }
});

function closeErrorWindow() {
    const errorWindow = document.getElementById('error-window');
    errorWindow.classList.remove('animate__fadeIn');
    errorWindow.classList.add('animate__fadeOut');
    setTimeout(() => {
        errorWindow.style.display = 'none';
    }, 500);
}
</script>

<style>
.error-window {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    padding: 20px;
    background-color: white;
    border: 1px solid #dc3545;
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    z-index: 10000;
}

.error-content {
    text-align: center;
}

.error-content h3 {
    color: #dc3545;
    margin-bottom: 15px;
}

.error-content p {
    margin-bottom: 20px;
}

.error-content button {
    width: 100px;
}
</style>
*/
?>