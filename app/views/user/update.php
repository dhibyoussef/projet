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
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    // Regenerate session ID for security
    if (!isset($_SESSION['initiated'])) {
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['initiated'] = true;
    }

    // Generate CSRF token with expiration if it doesn't exist or has expired
    if (!isset($_SESSION['csrf_token']) || (time() - $_SESSION['csrf_token_time']) > 3600) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        if (!$_SESSION['csrf_token']) {
            throw new Exception('Failed to generate CSRF token');
        }
        $_SESSION['csrf_token_time'] = time();
    }

    if (!include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    error_log('Error in user update view: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    // Store error message for display
    $_SESSION['message'] = 'An error occurred while loading the page. Please try again later.';
    $_SESSION['message_type'] = 'danger';
}
?>
<link rel="stylesheet" href="<?php echo htmlspecialchars('assets/bootstrap.css', ENT_QUOTES, 'UTF-8'); ?>">
<link rel="stylesheet"
    href="<?php echo htmlspecialchars('https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css', ENT_QUOTES, 'UTF-8'); ?>">
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
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translate(-50%, -40%);
    }

    100% {
        opacity: 1;
        transform: translate(-50%, -50%);
    }
}
</style>
<div class="container">
    <h1 class="mb-4">Update Profile</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="error-window">
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible"
            role="alert">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="close" onclick="dismissError()">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="POST"
        action="<?php echo htmlspecialchars('../../controllers/user/UpdateController.php', ENT_QUOTES, 'UTF-8'); ?>"
        class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name"
                value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please enter your name.</div>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email"
                value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please enter your email.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Update Profile</button>
    </form>
</div>
<script>
function dismissError() {
    const errorWindow = document.querySelector('.error-window');
    if (errorWindow) {
        errorWindow.remove();
    }
}
</script>
<?php 
try {
    if (!include '../layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    error_log('Error including footer: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}
?>