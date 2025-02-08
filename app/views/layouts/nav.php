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
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                throw new Exception('Failed to generate CSRF token');
            }
        }
    }
} catch (Exception $e) {
    // Log the error and store for window animation
    error_log('Navigation Error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'A system error occurred. Please try again later.';
    $_SESSION['error_animation'] = 'windowShake';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/">Fitness
            Tracker</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/workout">Workouts</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/nutrition">Nutrition</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/progress">Progress</a>
                </li>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/profile">Profile</a>
                </li>
                <li class="nav-item">
                    <form action="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/logout"
                        method="POST" class="nav-link-form">
                        <input type="hidden" name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <button type="submit" class="nav-link btn btn-link">Logout</button>
                    </form>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"
                        href="<?php echo htmlspecialchars($baseUrl ?? '', ENT_QUOTES, 'UTF-8'); ?>/signup">Sign Up</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php if (isset($_SESSION['error_message'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const errorWindow = document.createElement('div');
    errorWindow.className =
        'alert alert-danger animate__animated animate__<?php echo htmlspecialchars($_SESSION['error_animation'] ?? 'shakeX', ENT_QUOTES, 'UTF-8'); ?>';
    errorWindow.style.position = 'fixed';
    errorWindow.style.top = '20px';
    errorWindow.style.right = '20px';
    errorWindow.style.zIndex = '10000';
    errorWindow.innerHTML = `
            <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" onclick="this.parentElement.remove()">
                <span aria-hidden="true">&times;</span>
            </button>
        `;
    document.body.appendChild(errorWindow);

    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        errorWindow.classList.remove(
            'animate__<?php echo htmlspecialchars($_SESSION['error_animation'] ?? 'shakeX', ENT_QUOTES, 'UTF-8'); ?>'
            );
        errorWindow.classList.add('animate__fadeOut');
        setTimeout(() => errorWindow.remove(), 500);
    }, 5000);

    <?php unset($_SESSION['error_message']); unset($_SESSION['error_animation']); ?>
});
</script>
<?php endif; ?>