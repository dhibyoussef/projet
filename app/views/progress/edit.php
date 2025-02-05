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
    <h1 class="mb-4">Edit Progress Entry</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
        <?php echo $_SESSION['message']; ?>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="POST"
        action="../../controllers/progress/UpdateController.php?id=<?php echo htmlspecialchars($progress['id']); ?>"
        class="needs-validation" novalidate>
        <?php if (isset($_SESSION['csrf_token'])): ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
        <div class="alert alert-danger">CSRF token missing. Please refresh the page and try again.</div>
        <?php endif; ?>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date"
                value="<?php echo htmlspecialchars($progress['date']); ?>" required>
            <div class="invalid-feedback">Please select a valid date.</div>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" class="form-control" name="weight" id="weight"
                value="<?php echo htmlspecialchars($progress['weight']); ?>" required step="0.1">
            <div class="invalid-feedback">Please enter your weight.</div>
        </div>
        <div class="form-group">
            <label for="body_fat">Body Fat (%)</label>
            <input type="number" class="form-control" name="body_fat" id="body_fat"
                value="<?php echo htmlspecialchars($progress['body_fat']); ?>" required step="0.1">
            <div class="invalid-feedback">Please enter your body fat percentage.</div>
        </div>
        <button type="submit" class="btn btn-primary" name="ok">Update Entry</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>