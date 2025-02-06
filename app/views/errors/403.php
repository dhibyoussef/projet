s<?php 
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        if (!session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true, // secure
            true  // httponly
        )) {
            throw new Exception('Failed to set secure session cookie parameters');
        }
        
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }

    // Set page title
    $pageTitle = '403 Forbidden';

    // Include header with error handling
    $headerPath = '../../views/layouts/header.php';
    if (!file_exists($headerPath)) {
        throw new Exception('Header file not found');
    }
    include $headerPath;
} catch (Exception $e) {
    error_log("403 page initialization error: " . $e->getMessage());
    http_response_code(500);
    exit('An error occurred while loading the 403 page');
}
?>

<div class="container mt-5">
    <div class="window animate__animated animate__fadeIn"
        style="background: white; border: 1px solid #ccc; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <div class="title-bar" style="background: #f8f9fa; padding: 10px; border-bottom: 1px solid #ccc;">
            <h1 class="display-4" style="color: #dc3545; margin: 0;">403 Forbidden</h1>
        </div>
        <div class="content" style="padding: 20px;">
            <p class="lead">Access Denied: You do not have the necessary permissions to view this page.</p>
            <hr class="my-4">
            <p>Please verify your access rights or return to the <a href="/fitness_tracker/public/index.php"
                    class="alert-link">home page</a>.</p>
            <?php if (isset($_SESSION['error_message'])): ?>
            <div class="mt-3">
                <p class="mb-0">Additional information:</p>
                <p class="font-weight-bold"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
            </div>
            <?php 
                unset($_SESSION['error_message']);
            ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['csrf_token_error'])): ?>
            <div class="mt-3">
                <p class="mb-0">Security Notice:</p>
                <p class="font-weight-bold"><?php echo htmlspecialchars($_SESSION['csrf_token_error']); ?></p>
            </div>
            <?php 
                unset($_SESSION['csrf_token_error']);
            ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const windowElement = document.querySelector('.window');
    windowElement.classList.add('animate__animated', 'animate__fadeIn');

    windowElement.addEventListener('animationend', () => {
        windowElement.classList.remove('animate__animated', 'animate__fadeIn');
    });
});
</script>

<?php 
try {
    // Include footer with error handling
    $footerPath = '../../views/layouts/footer.php';
    if (!file_exists($footerPath)) {
        throw new Exception('Footer file not found');
    }
    include $footerPath;
} catch (Exception $e) {
    error_log("403 page footer error: " . $e->getMessage());
    http_response_code(500);
    exit('An error occurred while loading the page footer');
}
?>