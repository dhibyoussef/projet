<?php 
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../../views/layouts/header.php'; 
?>
<div class="container">
    <h1>404 Not Found</h1>
    <p>We're sorry, but the page you are looking for does not exist. It may have been removed, renamed, or is
        temporarily unavailable.</p>
    <p>Please check the URL for errors or return to the <a href="/fitness_tracker/public/index.php">home page</a>.</p>
</div>
<?php include '../../views/layouts/footer.php'; ?>