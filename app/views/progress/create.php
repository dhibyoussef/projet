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
<div class="container mt-5">
    <h1 class="mb-4">Log Your Progress</h1>
    <?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show" role="alert">
        <?php echo $_SESSION['message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <form method="POST" action="../../controllers/progress/CreateController.php" class="needs-validation" novalidate>
        <?php if (isset($_SESSION['csrf_token'])): ?>
        <input type="hidden" name="csrf_token"
            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
        <div class="alert alert-danger">CSRF token missing. Please refresh the page and try again.</div>
        <?php endif; ?>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" name="date" id="date" required>
            <div class="invalid-feedback">Please select a date.</div>
        </div>
        <div class="form-group">
            <label for="weight">Weight (kg)</label>
            <input type="number" class="form-control" name="weight" id="weight" step="0.1" required>
            <div class="invalid-feedback">Please enter your weight.</div>
        </div>
        <div class="form-group">
            <label for="body_fat">Body Fat (%)</label>
            <input type="number" class="form-control" name="body_fat" id="body_fat" step="0.1" required>
            <div class="invalid-feedback">Please enter your body fat percentage.</div>
        </div>
        <div class="form-group">
            <label for="muscle_mass">Muscle Mass (kg)</label>
            <input type="number" class="form-control" name="muscle_mass" id="muscle_mass" step="0.1">
            <div class="invalid-feedback">Please enter your muscle mass.</div>
        </div>
        <button type="submit" class="btn btn-success" name="create">Log Progress</button>
        <a href="/progress" class="btn btn-secondary">Cancel</a>
    </form>
</div>
<?php include '../layouts/footer.php'; ?>