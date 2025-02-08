<?php
// Start session with secure settings if not already started
try {
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
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            try {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
                throw new Exception('Failed to generate CSRF token: ' . $e->getMessage());
            }
        }
    }
} catch (Exception $e) {
    error_log('Session initialization error: ' . $e->getMessage());
    $_SESSION['error_message'] = 'An error occurred while initializing the session. Please try again later.';
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';">
    <meta name="csrf-token"
        content="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <link rel="stylesheet" href="/public/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.3.1/css/hover-min.css">

    <script src="/public/assets/js/validation.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker', ENT_QUOTES, 'UTF-8'); ?></title>
</head>

<body>
    <?php if (isset($_SESSION['error_message'])): ?>
    <div class="error-window animate__animated animate__shakeX"
        style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10000; padding: 20px; background-color: #ff4444; color: white; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.3); width: 300px; text-align: center;">
        <div style="font-size: 24px; margin-bottom: 10px;">⚠️</div>
        <div style="margin-bottom: 15px;">
            <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?></div>
        <button onclick="this.parentElement.remove()"
            style="background: none; border: 1px solid white; color: white; padding: 5px 15px; border-radius: 4px; cursor: pointer;">OK</button>
    </div>
    <?php unset($_SESSION['error_message']); endif; ?>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top animate__animated animate__fadeInDown">
            <a class="navbar-brand animate__animated animate__fadeInLeft" href="<?php echo htmlspecialchars('/index.php', ENT_QUOTES, 'UTF-8'); ?>">
                <i class="fas fa-dumbbell"></i> Fitness Tracker
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <?php if ((isset($_SESSION['logged_in']) && $_SESSION['logged_in'])): ?>
                    <a class="nav-link animate__animated animate__fadeIn" href="<?php echo htmlspecialchars('/index.php', ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <?php endif; ?>
                    <a class="nav-link animate__animated animate__fadeIn"
                        href="<?php echo htmlspecialchars('../../controllers/workout/ReadController.php', ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fas fa-running"></i> Workouts
                    </a>
                    <a class="nav-link animate__animated animate__fadeIn"
                        href="<?php echo htmlspecialchars('../../controllers/nutrition/ReadController.php', ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fas fa-utensils"></i> Nutrition
                    </a>
                    <a class="nav-link animate__animated animate__fadeIn"
                        href="<?php echo htmlspecialchars('../../controllers/progress/ReadController.php', ENT_QUOTES, 'UTF-8'); ?>">
                        <i class="fas fa-chart-line"></i> Progress
                    </a>
                    <?php if ((isset($_SESSION['logged_in']) && $_SESSION['logged_in'])): ?>
                    <div class="navbar-nav ml-auto">
                        <a class="nav-link animate__animated animate__fadeIn" href="<?php echo htmlspecialchars('../../views/user/profile.php', ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a class="nav-link animate__animated animate__fadeIn"
                            href="<?php echo htmlspecialchars('../../controllers/user/LogoutController.php', ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>