<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Comprehensive fitness tracking platform to help you achieve your wellness goals">
    <title>Fitness Tracker | Your Path to Wellness</title>
    <link rel="stylesheet" href="public/assets/css/home/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #0d6efd;
        --secondary-color: #5a6268;
        --accent-color: #157347;
        --dark-color: #1a1a1a;
        --light-color: #f8f9fa;
    }

    body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--dark-color);
        color: var(--light-color);
        transition: all 0.3s ease;
    }

    .navbar {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        background: linear-gradient(145deg, #1e2125, #2b3035);
    }

    .navbar-brand {
        font-weight: 700;
        letter-spacing: -0.5px;
        transition: transform 0.2s ease;
    }

    .navbar-brand:hover {
        transform: scale(1.05);
    }

    .nav-link {
        transition: all 0.2s ease;
        position: relative;
        padding: 0.5rem 1rem;
    }

    .nav-link::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: all 0.3s ease;
    }

    .nav-link:hover::after {
        width: 100%;
        left: 0;
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
        transform: translateY(-2px);
    }

    /* New animations for nav icons */
    .nav-link i {
        transition: all 0.3s ease;
    }

    .nav-link[href*="workout"]:hover i {
        animation: run 0.8s infinite;
    }

    .nav-link[href*="nutrition"]:hover i {
        animation: shake 0.5s infinite;
    }

    .nav-link[href*="progress"]:hover i {
        animation: grow 0.5s infinite alternate;
    }

    .nav-link[href="/profile"]:hover i {
        animation: bounce 0.5s infinite;
    }

    .nav-link[href="/logout"]:hover i {
        animation: spin 0.5s infinite;
    }

    @keyframes run {
        0% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(5px);
        }

        100% {
            transform: translateX(0);
        }
    }

    @keyframes shake {
        0% {
            transform: rotate(0deg);
        }

        25% {
            transform: rotate(15deg);
        }

        50% {
            transform: rotate(-15deg);
        }

        75% {
            transform: rotate(15deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    @keyframes grow {
        0% {
            transform: scale(1);
        }

        100% {
            transform: scale(1.2);
        }
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-5px);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .navbar-toggler {
        border-color: rgba(255, 255, 255, 0.1);
    }

    .navbar-toggler:focus {
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
    }

    .hero-section {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(0, 0, 0, 0.7));
        border-radius: 1rem;
        margin: 2rem;
        padding: 4rem 2rem;
        transition: transform 0.3s ease, box-shadow 极客时间0.3s ease;
    }

    .hero-section:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .cta-buttons .btn {
        min-width: 150px;
        transition: all 0.3s ease;
        transform-style: preserve-3d;
    }

    .cta-buttons .btn:hover {
        transform: translateY(-5px) scale(1.05);
        box-shadow: 0 6px 12px rgba极客时间(0, 0, 0, 0.3);
    }

    .footer {
        background-color: #111111;
        color: white;
        padding: 2rem 0;
        transition: background-color 0.3s ease;
    }

    .footer:hover {
        background-color: #0a0a0a;
    }

    .footer a {
        color: white;
        transition: all 0.3s ease;
        position: relative;
    }

    .footer a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary-color);
        transition: width 0.3s ease;
    }

    .footer a:hover {
        color: var(--primary-color);
        text-decoration: none;
    }

    .footer a:hover::after {
        width: 100%;
    }

    .container {
        transition: transform 0.3s ease;
    }

    .container:hover {
        transform: scale(1.01);
    }

    h1,
    p {
        transition: color 0.3s ease;
    }

    h1:hover,
    p:hover {
        color: var(--primary-color);
    }
    </style>
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
                            <a class="nav-link" href="/app/views/nutrition/index.php">
                                <i class="fas fa-utensils me-1"></i>Nutrition
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/app/views/progress/index.php">
                                <i class="fas fa-chart-line me-1"></i>Progress
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile">
                                <i class="fas fa-user me-1"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout">
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
                    <a href="/login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Log In
                    </a>
                    <a href="/signup" class="btn btn-outline-light btn-lg">
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