<?php
session_start(); // Start the session

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Comprehensive fitness tracking platform to help you achieve your wellness goals">
    <title>Fitness Tracker | Your Path to Wellness</title>
    <link rel="stylesheet" href="public/assets/css/home/bootstrap.css">
    <link rel="stylesheet" href="public/assets/css/home/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <meta name="csrf-token"
        content="<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') : ''; ?>">
    <style>
    body,
    html {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background: linear-gradient(135deg, #1a1a1a 0%, #2c3e50 100%);
        color: white;
        font-family: Arial, sans-serif;
        perspective: 1000px;
        transform-style: preserve-3d;
    }

    .dashboard-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        height: 100vh;
        width: 100vw;
        gap: 0;
        margin: 0;
        padding: 0;
        transform-style: preserve-3d;
    }

    .dashboard-card {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
        transition: all 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
        background-size: cover;
        background-position: center;
        transform-style: preserve-3d;
        perspective: 1000px;
        border: none;
        height: 100vh;
        width: 100%;
        overflow: hidden;
        transform: translateZ(0);
    }

    .dashboard-card:hover {
        transform: scale(1.2) translateZ(50px);
        z-index: 100;
        box-shadow: 0 0 50px rgba(0, 0, 0, 0.8);
    }

    .dashboard-card h2 {
        font-size: 4rem;
        font-weight: bold;
        z-index: 2;
        transition: all 0.3s ease;
        transform: translateZ(50px);
        text-align: center;
    }

    .dashboard-card:hover h2 {
        transform: scale(1.3) translateZ(100px);
    }

    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1;
        transition: all 0.3s ease;
        transform: translateZ(0);
    }

    .dashboard-card:hover::before {
        background: rgba(0, 0, 0, 0.1);
        transform: translateZ(20px);
    }

    .workout-card {
        background-image: url('/app/views/workout/assets/workout.jpg');
        transform: rotateY(0deg) translateZ(0);
    }

    .nutrition-card {
        background-image: url('/app/views/nutrition/assets/nutrition.jpg');
        transform: rotateY(0deg) translateZ(0);
    }

    .progress-card {
        background-image: url('/app/views/progress/assets/progress.jpg');
        transform: rotateY(0deg) translateZ(0);
    }

    .user-menu {
        position: fixed;
        top: 30px;
        right: 30px;
        z-index: 1000;
        display: flex;
        gap: 1.5rem;
        transform-style: preserve-3d;
    }

    .user-menu .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateZ(20px);
    }

    .user-menu .btn-primary:hover {
        transform: translateY(-2px) translateZ(30px);
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.3);
    }

    .user-menu .btn-danger:hover {
        transform: translateY(-2px) translateZ(30px);
        box-shadow: 0 6px 12px rgba(220, 53, 69, 0.3);
    }

    .navbar {
        background: linear-gradient(145deg, #1a1a1a, #2c3e50) !important;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        transform: translateZ(0);
        perspective: 1000px;
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        transform: translateZ(20px);
        transition: all 0.3s ease;
    }

    .navbar-brand:hover {
        transform: scale(1.05) translateZ(30px);
        text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7);
    }

    .nav-item {
        position: relative;
        margin: 0 10px;
        transform-style: preserve-3d;
    }

    .nav-link {
        position: relative;
        padding: 10px 20px !important;
        border-radius: 25px;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transform: translateZ(20px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
    }

    .nav-link:hover {
        transform: scale(1.1) translateZ(30px);
        background: rgba(255, 255, 255, 0.1);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
    }

    .nav-link i {
        margin-right: 8px;
        transform: translateZ(10px);
    }

    .navbar-toggler {
        border: 1px solid rgba(255, 255, 255, 0.2);
        transform: translateZ(20px);
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(255, 255, 255, 0.8)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    .hero-section {
        position: relative;
        padding: 100px 0;
        transform-style: preserve-3d;
        perspective: 1000px;
    }

    .display-4 {
        font-size: 3.5rem;
        font-weight: 700;
        text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
        transform: translateZ(50px);
    }

    .lead {
        font-size: 1.25rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        transform: translateZ(30px);
    }

    .cta-buttons .btn {
        transform: translateZ(20px);
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .cta-buttons .btn:hover {
        transform: scale(1.1) translateZ(30px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.5);
    }
    </style>
</head>

<body class="d-flex flex-column h-100">
    <?php if ($isLoggedIn): ?>
    <div class="user-menu">
        <a href="/app/views/user/profile.php" class="btn btn-primary">
            <i class="fas fa-user me-1"></i>Profile
        </a>
        <form action="/app/views/user/logout.php" method="POST" class="d-inline">
            <input type="hidden" name="csrf_token"
                value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </button>
        </form>
    </div>

    <div class="dashboard-section">
        <a href="/app/controllers/workout/ReadController.php" class="dashboard-card workout-card">
            <h2>Workouts</h2>
        </a>
        <a href="/app/controllers/nutrition/ReadController.php" class="dashboard-card nutrition-card">
            <h2>Nutrition</h2>
        </a>
        <a href="/app/controllers/progress/ReadController.php" class="dashboard-card progress-card">
            <h2>Progress</h2>
        </a>
    </div>
    <?php else: ?>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <i class="fas fa-dumbbell me-2"></i>Fitness Tracker
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/app/controllers/workout/ReadController.php">
                                <i class="fas fa-running me-1"></i>Workouts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/app/controllers/nutrition/ReadController.php">
                                <i class="fas fa-utensils me-1"></i>Nutrition
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/app/controllers/progress/ReadController.php">
                                <i class="fas fa-chart-line me-1"></i>Progress
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-shrink-0">
        <div class="container my-5">
            <div class="hero-section text-center">
                <?php 
                if (file_exists('app/views/layouts/messages.php')) {
                    include 'app/views/layouts/messages.php';
                } else {
                    error_log('Messages file not found');
                }
                ?>
                <h1 class="display-4 fw-bold mb-4">Welcome to Your Fitness Journey</h1>
                <p class="lead mb-5">Track your progress, achieve your goals, and transform your life with our
                    comprehensive fitness platform.</p>
                <div class="cta-buttons d-flex justify-content-center gap-3">
                    <a href="/app/views/user/login.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Log In
                    </a>
                    <a href="/app/views/user/signup.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </a>
                </div>
            </div>
        </div>
    </main>
    <?php endif; ?>

    <footer class="footer mt-auto py-4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-3">&copy; <?php echo htmlspecialchars(date("Y"), ENT_QUOTES, 'UTF-8'); ?> Fitness
                        Tracker. All rights reserved.</p>
                    <nav class="nav justify-content-center">
                        <a href="/privacy" class="nav-link px-2">Privacy Policy</a>
                        <a href="/terms" class="nav-link px-2">Terms of Service</a>
                        <a href="/contact" class="nav-link px-2">Contact Us</a>
                    </nav>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>