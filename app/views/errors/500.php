<?php
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params(
            $sessionParams["lifetime"],
            $sessionParams["path"],
            $sessionParams["domain"],
            true, // secure
            true  // httponly
        );
        session_start();
        
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Set page title
    $pageTitle = htmlspecialchars('500 Internal Server Error', ENT_QUOTES, 'UTF-8');

    // Include header with error handling
    if (!@include '../../views/layouts/header.php') {
        throw new Exception('Failed to load header template');
    }

    // Add window shake animation styles
    echo htmlspecialchars('<style>
        @keyframes windowShake {
            0% { transform: translate(0, 0); }
            25% { transform: translate(-10px, 10px); }
            50% { transform: translate(10px, -10px); }
            75% { transform: translate(-10px, 10px); }
            100% { transform: translate(0, 0); }
        }
        .window-shake {
            animation: windowShake 0.5s;
        }
    </style>', ENT_QUOTES, 'UTF-8');
    ?>

<div class="container mt-5">
    <div class="alert alert-danger <?php echo isset($_SESSION['error_animation']) ? htmlspecialchars('window-shake', ENT_QUOTES, 'UTF-8') : ''; ?>"
        role="alert">
        <h1 class="display-4">500 Internal Server Error</h1>
        <p class="lead">Something went wrong on our end. Please try again later.</p>
        <hr class="my-4">
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mb-3">
            <p class="mb-0">Error Details:</p>
            <p class="font-weight-bold"><?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php unset($_SESSION['error_animation']); ?>
        <?php endif; ?>
        <p>If the problem persists, please contact our support team.</p>
        <a href="<?php echo htmlspecialchars('/fitness_tracker/public/index.php', ENT_QUOTES, 'UTF-8'); ?>"
            class="btn btn-primary">Return to Home</a>
    </div>
</div>

<?php 
    // Include footer with error handling
    if (!@include '../../views/layouts/footer.php') {
        throw new Exception('Failed to load footer template');
    }
} catch (Exception $e) {
    // Enhanced error display with window shake animation
    echo htmlspecialchars('<style>
        @keyframes windowShake {
            0% { transform: translate(0, 0); }
            25% { transform: translate(-10px, 10px); }
            50% { transform: translate(10px, -10px); }
            75% { transform: translate(-10px, 10px); }
            100% { transform: translate(0, 0); }
        }
        .window-shake {
            animation: windowShake 0.5s;
        }
    </style>', ENT_QUOTES, 'UTF-8');
    echo htmlspecialchars('<div class="container mt-5"><div class="alert alert-danger window-shake" role="alert">', ENT_QUOTES, 'UTF-8');
    echo htmlspecialchars('<h1>Critical Error</h1>', ENT_QUOTES, 'UTF-8');
    echo htmlspecialchars('<p>An unexpected error occurred while displaying this page.</p>', ENT_QUOTES, 'UTF-8');
    echo htmlspecialchars('<p>Error: ' . $e->getMessage() . '</p>', ENT_QUOTES, 'UTF-8');
    echo htmlspecialchars('</div></div>', ENT_QUOTES, 'UTF-8');
}
?>