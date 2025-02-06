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

</head>

<body class="d-flex flex-column h-100">
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
                            <a class="nav-link" href="app/controllers/workout/ReadController.php">
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
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/app/views/user/profile.php">
                                <i class="fas fa-user me-1"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/app/views/user/logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="flex-shrink-0">
        <div class="container my-5">
            <div class="hero-section text-center">
                <?php include 'app/views/layouts/messages.php'; ?>
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

    <footer class="footer mt-auto py-4">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-3">&copy; <?php echo date("Y"); ?> Fitness Tracker. All rights reserved.</p>
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