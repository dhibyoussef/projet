<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: /login');
    exit();
}

include '../layouts/header.php'; 
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Delete Account</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type']); ?> alert-dismissible fade show"
        role="alert">
        <?php echo htmlspecialchars($_SESSION['message']); ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <p>Are you sure you want to delete your account? This action cannot be undone.</p>
    <form method="POST" action="../../controllers/user/DeleteController.php">
        <?php 
        // Generate CSRF token if not already set
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time(); // Store token generation time
        }
        ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-danger" name="ok"
            onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete
            My Account</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>