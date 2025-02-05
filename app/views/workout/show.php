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

// Regenerate session ID for security
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

include '../layouts/header.php'; 
?>
<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <?php 
    // Generate CSRF token if not already set
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time(); // Store token generation time
    }
    ?>
    <h1 class="mb-4"><?php echo htmlspecialchars($workout['name'], ENT_QUOTES, 'UTF-8'); ?></h1>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($workout['description'], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Duration:</strong> <?php echo htmlspecialchars($workout['duration'], ENT_QUOTES, 'UTF-8'); ?> minutes</p>
    <form action="edit.php" method="GET" style="display: inline;">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-warning">Edit Workout</button>
    </form>
    <form action="index.php" method="GET" style="display: inline;">
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-secondary">Back to Workouts</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>