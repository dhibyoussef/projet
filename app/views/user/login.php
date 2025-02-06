<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID for security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

include '../layouts/header.php'; 
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
body {
    background-image: url('assets/g.png');
    background-size: cover;
}
</style>
<div class="container">
    <h1 class="mb-4">Login</h1>
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
    <form method="POST" action="../../controllers/user/LoginController.php" class="needs-validation" novalidate>
        <?php 
        // Generate CSRF token if not already set
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time(); // Store token generation time
        }
        ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" id="email" required>
            <div class="invalid-feedback">Please enter your email.</div>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
            <div class="invalid-feedback">Please enter your password.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="login">Login</button>
    </form>
    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up here</a>.</p>
</div>
<?php include '../layouts/footer.php'; ?>