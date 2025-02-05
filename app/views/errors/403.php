<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page title
$pageTitle = '403 Forbidden';

// Include header
include '../../views/layouts/header.php'; 
?>

<div class="container mt-5">
    <div class="alert alert-danger" role="alert">
        <h1 class="display-4">403 Forbidden</h1>
        <p class="lead">Access Denied: You do not have the necessary permissions to view this page.</p>
        <hr class="my-4">
        <p>Please verify your access rights or return to the <a href="/fitness_tracker/public/index.php"
                class="alert-link">home page</a>.</p>
        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="mt-3">
            <p class="mb-0">Additional information:</p>
            <p class="font-weight-bold"><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    </div>
</div>

<?php 
// Include footer
include '../../views/layouts/footer.php'; 
?>