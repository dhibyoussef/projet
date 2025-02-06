<?php
// Start session with secure settings if not already started
if (session_status() === PHP_SESSION_NONE) {
    try {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ])) {
            throw new Exception('Failed to set secure session parameters');
        }
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Initialize $_SESSION array if not set
        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }
        
        // Generate CSRF token if it doesn't exist or has expired
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_expire']) || time() > $_SESSION['csrf_token_expire']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_expire'] = time() + 3600; // Token expires in 1 hour
        }
    } catch (Exception $e) {
        error_log('Session initialization error: ' . $e->getMessage());
        die('An error occurred while initializing the session. Please try again later.');
    }
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /login');
    exit();
}

// Include header with error handling
if (!@include '../layouts/header.php') {
    die('Failed to load required components. Please try again later.');
}

// Initialize $progress as array if not set
if (!isset($progress) || !is_array($progress)) {
    $progress = [];
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<style>
.error-window {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    padding: 20px;
    background-color: #fff;
    border: 1px solid #dc3545;
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    z-index: 1000;
}
</style>
<div class="container">
    <h1 class="mb-4">Progress Entry Details</h1>
    <div id="message-container" class="animate__animated"></div>
    <div class="card">
        <div class="card-body">
            <?php if (empty($progress)): ?>
            <div class="error-window animate__animated animate__shakeX">
                <h5 class="text-danger">Error</h5>
                <p>No progress data found.</p>
                <button class="btn btn-sm btn-danger" onclick="dismissError()">Close</button>
            </div>
            <?php else: ?>
            <p><strong>Date:</strong>
                <?php echo htmlspecialchars(date('F j, Y', strtotime($progress['date'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p><strong>Weight:</strong> <?php echo htmlspecialchars($progress['weight'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                kg</p>
            <p><strong>Body Fat:</strong>
                <?php echo htmlspecialchars($progress['body_fat'] ?? '', ENT_QUOTES, 'UTF-8'); ?> %
            </p>
            <?php endif; ?>
        </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <?php if (!empty($progress['id'])): ?>
        <a href="edit.php?id=<?php echo htmlspecialchars($progress['id'], ENT_QUOTES, 'UTF-8'); ?>"
            class="btn btn-warning" role="button" aria-label="Edit progress entry">
            <i class="fas fa-edit"></i> Edit Entry
        </a>
        <?php else: ?>
        <button class="btn btn-warning" disabled aria-disabled="true">
            <i class="fas fa-edit"></i> Edit Entry
        </button>
        <?php endif; ?>
        <a href="index.php" class="btn btn-secondary" role="button" aria-label="Return to progress log">
            <i class="fas fa-arrow-left"></i> Back to Progress Log
        </a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageContainer = document.getElementById('message-container');

    function showMessage(message, type) {
        messageContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show animate__animated animate__bounceIn" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;

        // Remove message after 5 seconds
        setTimeout(() => {
            const alert = messageContainer.querySelector('.alert');
            if (alert) {
                alert.classList.remove('animate__bounceIn');
                alert.classList.add('animate__fadeOut');
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }
        }, 5000);
    }

    function dismissError() {
        const errorWindow = document.querySelector('.error-window');
        if (errorWindow) {
            errorWindow.classList.remove('animate__shakeX');
            errorWindow.classList.add('animate__fadeOut');
            setTimeout(() => {
                errorWindow.remove();
            }, 500);
        }
    }

    // Check for session message
    <?php if (isset($_SESSION['message'])): ?>
    showMessage(`<?php echo addslashes($_SESSION['message']); ?>`, '<?php echo $_SESSION['message_type']; ?>');
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
});
</script>
<?php 
// Include footer with error handling
if (!@include '../layouts/footer.php') {
    error_log('Failed to load footer');
}
?>