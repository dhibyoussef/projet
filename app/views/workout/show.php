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
        if (!headers_sent()) {
            header('Location: /login');
        }
        exit();
    }

    // Regenerate session ID for security
    if (!isset($_SESSION['initiated'])) {
        if (!session_regenerate_id(true)) {
            throw new Exception('Failed to regenerate session ID');
        }
        $_SESSION['initiated'] = true;
    }

    if (!include '../layouts/header.php') {
        throw new Exception('Failed to include header file');
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger animate__animated animate__bounce" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    error_log('Error in workout/show.php: ' . $e->getMessage());
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<div class="container">
    <?php 
    try {
        // Generate CSRF token with expiration time (15 minutes)
        $csrf_expiration = 15 * 60; // 15 minutes in seconds
        
        // Generate new CSRF token if not set or expired
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) || 
            time() > ($_SESSION['csrf_token_time'] + $csrf_expiration)) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            if (!$_SESSION['csrf_token']) {
                throw new Exception('Failed to generate CSRF token');
            }
            $_SESSION['csrf_token_time'] = time();
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger animate__animated animate__bounce" role="alert">Security Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        error_log('CSRF token generation error: ' . $e->getMessage());
    }
    ?>
    <h1 class="mb-4"><?php echo htmlspecialchars($workout['name'] ?? 'Unknown Workout', ENT_QUOTES, 'UTF-8'); ?></h1>
    <p><strong>Description:</strong>
        <?php echo htmlspecialchars($workout['description'] ?? 'No description available', ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Duration:</strong> <?php echo htmlspecialchars($workout['duration'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>
        minutes</p>
    <form action="edit.php" method="GET" style="display: inline;">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="id"
            value="<?php echo htmlspecialchars($workout['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-warning">Edit Workout</button>
    </form>
    <form action="index.php" method="GET" style="display: inline;">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-secondary">Back to Workouts</button>
    </form>
</div>
<?php 
try {
    if (!include '../layouts/footer.php') {
        throw new Exception('Failed to include footer file');
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger animate__animated animate__bounce" role="alert">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    error_log('Error including footer: ' . $e->getMessage());
}
?>