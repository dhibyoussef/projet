<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';">
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/bootstrap.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/styles.css"> <!-- Custom CSS -->
    <script src="/fitness_tracker/public/assets/js/validation.js"></script>
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker'); ?></title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="/fitness_tracker/public/index.php">Fitness Tracker</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="/fitness_tracker/public/index.php">Home</a>
                    <a class="nav-link" href="/fitness_tracker/controllers/workout/ReadController.php">Workouts</a>
                    <a class="nav-link" href="/fitness_tracker/controllers/nutrition/ReadController.php">Nutrition</a>
                    <a class="nav-link" href="/fitness_tracker/controllers/progress/ReadController.php">Progress</a>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                    <a class="nav-link" href="/fitness_tracker/controllers/user/LogoutController.php">Logout</a>
                    <a class="nav-link" href="/fitness_tracker/views/user/profile.php">Profile</a>
                    <?php else: ?>
                    <a class="nav-link" href="/fitness_tracker/views/user/login.php">Login</a>
                    <a class="nav-link" href="/fitness_tracker/views/user/signup.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
    <?php include 'messages.php'; ?>