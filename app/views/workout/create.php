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

include '../../views/layouts/header.php'; 
?>

<link rel="stylesheet" href="assets/bootstrap.css">
<div class="container">
    <h1 class="mb-4">Add New Workout</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="POST" action="../../controllers/workout/CreateController.php" class="needs-validation" novalidate>
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
            <label for="workoutName">Workout Name</label>
            <input type="text" class="form-control" name="workoutName" id="workoutName" placeholder="Enter workout name"
                required>
            <div class="invalid-feedback">Please provide a valid workout name.</div>
        </div>
        <div class="form-group">
            <label for="workoutDescription">Description</label>
            <textarea class="form-control" name="workoutDescription" id="workoutDescription" rows="3"
                placeholder="Enter a brief description" required></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="form-group">
            <label for="workoutDuration">Duration (minutes)</label>
            <input type="number" class="form-control" name="workoutDuration" id="workoutDuration" min="1" required>
            <div class="invalid-feedback">Please enter a valid duration (minimum 1 minute).</div>
        </div>
        <button type="submit" class="btn btn-primary" name="ok">Add Workout</button>
    </form>
</div>
<?php include '../../views/layouts/footer.php'; ?>