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
            throw new Exception('Failed to set secure session parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if not already set
        if (!isset($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }

    if (!@include '../../views/layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    $_SESSION['error_animation'] = 'windowShake';
}

// Add window shake animation styles
echo '<style>
    @keyframes windowShake {
        0% { transform: translate(0, 0); }
        25% { transform: translate(-10px, 10px); }
        50% { transform: translate(10px, -10px); }
        75% { transform: translate(-10px, 10px); }
        100% { transform: translate(0, 0); }
    }
    .error-container {
        padding: 20px;
        margin: 20px 0;
        border: 1px solid #ff4444;
        background-color: #ffebee;
        border-radius: 4px;
        animation: windowShake 0.5s;
        box-shadow: 0 0 10px rgba(255, 0, 0, 0.3);
    }
</style>';
?>
<div class="container">
    <div class="error-container">
        <h1>404 Not Found</h1>
        <p>We're sorry, but the page you are looking for does not exist. It may have been removed, renamed, or is
            temporarily unavailable.</p>
        <p>Please check the URL for errors or return to the <a href="/fitness_tracker/public/index.php">home page</a>.
        </p>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['csrf_token'])): ?>
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <?php endif; ?>
    </div>
</div>
<?php 
try {
    if (!@include '../../views/layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    $_SESSION['error_animation'] = 'windowShake';
}
?>