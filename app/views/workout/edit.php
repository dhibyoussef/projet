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
    <h1 class="mb-4">Edit Workout</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="POST"
        action="../../controllers/workout/UpdateController.php?id=<?php echo htmlspecialchars($workout['id'], ENT_QUOTES, 'UTF-8'); ?>"
        class="needs-validation" novalidate>
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
            <label for="name">Workout Name</label>
            <input type="text" class="form-control" name="name" id="name"
                value="<?php echo htmlspecialchars($workout['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please provide a valid workout name.</div>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" id="description" rows="3"
                required><?php echo htmlspecialchars($workout['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            <div class="invalid-feedback">Please provide a description.</div>
        </div>
        <div class="form-group">
            <label for="duration">Duration (minutes)</label>
            <input type="number" class="form-control" name="duration" id="duration"
                value="<?php echo htmlspecialchars($workout['duration'], ENT_QUOTES, 'UTF-8'); ?>" required>
            <div class="invalid-feedback">Please enter the duration of the workout.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="update">Update Workout</button>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>