<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy"
        content="default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';">
    <link rel="stylesheet" href="/public/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hover.css/2.3.1/css/hover-min.css">


    <script src="/public/assets/js/validation.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js" defer></script>
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker'); ?></title>



</head>

<body>
    <header>
        <nav class=" navbar navbar-expand-lg navbar-dark fixed-top animate__animated animate__fadeInDown">
            <a class="navbar-brand animate__animated animate__fadeInLeft" href="/index.php">
                <i class="fas fa-dumbbell"></i> Fitness Tracker
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <?php if ((isset($_SESSION['logged_in']) && $_SESSION['logged_in'])): ?>
                    <a class="nav-link animate__animated animate__fadeIn" href="/index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                    <?php endif; ?>
                    <a class="nav-link animate__animated animate__fadeIn"
                        href="../../controllers/workout/ReadController.php">
                        <i class="fas fa-running"></i> Workouts
                    </a>
                    <a class="nav-link animate__animated animate__fadeIn"
                        href="../../controllers/nutrition/ReadController.php">
                        <i class="fas fa-utensils"></i> Nutrition
                    </a>
                    <a class="nav-link animate__animated animate__fadeIn"
                        href="../../controllers/progress/ReadController.php">
                        <i class="fas fa-chart-line"></i> Progress
                    </a>
                    <?php if ((isset($_SESSION['logged_in']) && $_SESSION['logged_in'])): ?>
                    <div class="navbar-nav ml-auto">
                        <a class="nav-link animate__animated animate__fadeIn" href="../../views/user/profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a class="nav-link animate__animated animate__fadeIn"
                            href="../../controllers/user/LogoutController.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>