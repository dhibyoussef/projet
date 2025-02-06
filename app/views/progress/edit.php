<?php
try {
    // Start session with secure settings if not already started
    if (session_status() === PHP_SESSION_NONE) {
        $sessionParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $sessionParams['lifetime'],
            'path' => '/',
            'domain' => $sessionParams['domain'],
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        if (!session_start()) {
            throw new Exception('Failed to start session');
        }
        
        // Generate CSRF token if not already set
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    // Check if user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: /login');
        exit();
    }

    // Check if progress data exists
    if (!isset($progress) || empty($progress)) {
        throw new Exception('Progress data not found');
    }

    include '../layouts/header.php'; 
} catch (Exception $e) {
    // Instead of redirecting, show error in the same page
    $errorMessage = $e->getMessage();
}
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<div class="container">
    <h1 class="mb-4">Edit Progress Entry</h1>

    <div id="message-container" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
        <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
            <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger animate__animated animate__shakeX" role="alert">
            <?php echo htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?> animate__animated animate__fadeIn"
            role="alert">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </div>

    <form method="POST"
        action="../../controllers/progress/UpdateController.php?id=<?php echo htmlspecialchars($progress['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
        class="needs-validation" novalidate>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date"
                value="<?php echo htmlspecialchars($progress['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please select a valid date.</div>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" class="form-control" name="weight" id="weight"
                value="<?php echo htmlspecialchars($progress['weight'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                step="0.1" min="0">
            <div class="invalid-feedback">Please enter a valid weight (minimum 0).</div>
        </div>
        <div class="form-group">
            <label for="body_fat">Body Fat (%)</label>
            <input type="number" class="form-control" name="body_fat" id="body_fat"
                value="<?php echo htmlspecialchars($progress['body_fat'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required
                step="0.1" min="0" max="100">
            <div class="invalid-feedback">Please enter a valid body fat percentage (0-100).</div>
        </div>
        <button type="submit" class="btn btn-primary" name="ok">Update Entry</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>