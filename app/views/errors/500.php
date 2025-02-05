<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set page title
$pageTitle = '500 Internal Server Error';

// Include header
include '../../views/layouts/header.php'; 
?>

<div class="container mt-5">
    <div class="alert alert-danger" role="alert">
        <h1 class="display-4">500 Internal Server Error</h1>
        <p class="lead">Something went wrong on our end. Please try again later.</p>
        <hr class="my-4">
        <p>If the problem persists, please contact our support team.</p>
        <a href="/fitness_tracker/public/index.php" class="btn btn-primary">Return to Home</a>
    </div>
</div>

<?php include '../../views/layouts/footer.php'; ?>