<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/bootstrap.css"> <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/fitness_tracker/public/assets/css/styles.css"> <!-- Custom CSS -->
    <script src="/fitness_tracker/public/assets/js/validation.js"></script>
    <title><?php echo htmlspecialchars($title ?? 'Fitness Tracker'); ?></title>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <a class="navbar-brand" href="/../../index.php">Fitness Tracker</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="navbar-nav">
                    <a class="nav-link" href="/../../index.php">Home</a>
                    <a class="nav-link" href="../../controllers/workout/ReadController.php">Workouts</a>
                    <a class="nav-link" href="../../controllers/nutrition/ReadController.php">Nutrition</a>
                    <a class="nav-link" href="../../controllers/progress/ReadController.php">Progress</a>
                    <a class="nav-link" href="../../views/user/login.php">Login</a>
                    <a class="nav-link" href="../../views/user/signup.php">Sign Up</a>
                </div>
            </div>
        </nav>
    </header>
    <?php include 'messages.php'; ?>