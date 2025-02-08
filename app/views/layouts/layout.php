<?php
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new Exception('Failed to set secure session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist and set expiration
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
            $_SESSION['csrf_token_expire'] = time() + 3600; // 1 hour expiration
        }
    }
} catch (Exception $e) {
    error_log("Layout initialization error: " . $e->getMessage());
    http_response_code(500);
    $_SESSION['error_message'] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';">
    <meta name="csrf-token"
        content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/style.css">
    <style>
    .error-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px;
        background-color: #ff4444;
        color: white;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        animation: windowOpen 0.5s ease-out;
        z-index: 1000;
    }

    @keyframes windowOpen {
        0% {
            transform: scale(0);
            opacity: 0;
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>
    <main class="container">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message" id="error-message">
            <?php echo htmlspecialchars($_SESSION['error_message'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        <?php include 'messages.php'; ?>
        <?php echo htmlspecialchars($content ?? '', ENT_QUOTES, 'UTF-8'); ?>
    </main>
    <?php include 'footer.php'; ?>
    <script src="/fitness_tracker/public/assets/js/main.js"></script>
    <script>
    // Store CSRF token in JavaScript variable for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Function to refresh CSRF token
    function refreshCsrfToken() {
        fetch('/fitness_tracker/public/csrf_refresh.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.token) {
                    document.querySelector('meta[name="csrf-token"]').content = data.token;
                    csrfToken = data.token;
                }
            })
            .catch(error => {
                showErrorMessage('Error refreshing CSRF token: ' + error.message);
                // Attempt to refresh again in 1 minute if failed
                setTimeout(refreshCsrfToken, 60000);
            });
    }

    // Function to show error messages
    function showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);

        // Auto-remove after 5 seconds with window close animation
        setTimeout(() => {
            errorDiv.style.animation = 'windowClose 0.3s ease-in';
            errorDiv.addEventListener('animationend', () => {
                errorDiv.remove();
            });
        }, 5000);
    }

    // Add window close animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes windowClose {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);

    // Refresh CSRF token every 30 minutes
    setInterval(refreshCsrfToken, 1800000);

    // Auto-remove existing error message after 5 seconds with window close animation
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        setTimeout(() => {
            errorMessage.style.animation = 'windowClose 0.3s ease-in';
            errorMessage.addEventListener('animationend', () => {
                errorMessage.remove();
            });
        }, 5000);
    }
    </script>
</body>

</html>